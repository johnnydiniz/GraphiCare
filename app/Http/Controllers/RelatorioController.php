<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Fatura;
use App\Models\Boleto;
use App\Models\MateriaPrima;
use App\Models\Estoque;
use App\Models\PerdaQuebra;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Controlador responsável pela geração de relatórios do sistema.
 *
 * @package App\Http\Controllers
 */
class RelatorioController extends Controller
{
    /**
     * Gera o relatório de ordens de serviço.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function ordens(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->format('Y-m-d'));
        $clienteId = $request->input('cliente_id');

        $query = OrdemServico::with('cliente.pessoa')
            ->whereBetween('created_at', [$dataInicio, Carbon::parse($dataFim)->endOfDay()]);

        if ($clienteId) {
            $query->where('cliente_id', $clienteId);
        }

        $ordens = $query->orderByDesc('created_at')->get();

        $totalOS = $ordens->count();
        $concluidas = $ordens->where('status', 'finalizada')->count();
        $pendentes = $ordens->where('status', 'pendente')->count();
        $receitaTotal = $ordens->where('status', 'finalizada')->sum('valor_final');

        $clientes = Cliente::with('pessoa')->where('ativo', true)->get()
            ->sortBy(fn ($c) => $c->pessoa->nome_social ?? $c->pessoa->nome_registro);

        return view('relatorio.ordens', compact(
            'dataInicio',
            'dataFim',
            'clienteId',
            'ordens',
            'totalOS',
            'concluidas',
            'pendentes',
            'receitaTotal',
            'clientes'
        ));
    }

    /**
     * Gera o relatório financeiro com faturas e boletos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function financeiro(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->format('Y-m-d'));
        $clienteId = $request->input('cliente_id');

        $queryFaturas = Fatura::with('ordemServico.cliente.pessoa')
            ->whereBetween('data_vencimento', [$dataInicio, $dataFim]);

        if ($clienteId) {
            $queryFaturas->whereHas('ordemServico', fn ($q) => $q->where('cliente_id', $clienteId));
        }

        $faturas = $queryFaturas->get();

        $boletos = Boleto::with('ordemCompra.fornecedor.pessoa')
            ->whereBetween('data_vencimento', [$dataInicio, $dataFim])
            ->get();

        $totalFaturas = $faturas->count();
        $totalBoletos = $boletos->count();
        $totalAReceber = $faturas->whereIn('status', ['pendente', 'vencida'])->sum('valor');
        $totalAPagar = $boletos->whereIn('status', ['pendente', 'vencido'])->sum('valor');
        $saldo = $faturas->where('status', 'paga')->sum('valor') - $boletos->where('status', 'pago')->sum('valor');

        // Movimentações unificadas
        $movimentacoes = collect();

        foreach ($faturas as $fatura) {
            $cliente = $fatura->ordemServico->cliente->pessoa->nome_social
                ?? $fatura->ordemServico->cliente->pessoa->nome_registro
                ?? '-';
            $movimentacoes->push([
                'tipo' => 'Fatura',
                'referencia' => 'OS #' . $fatura->ordem_servico_id,
                'entidade' => $cliente,
                'valor' => $fatura->valor,
                'status' => $fatura->status,
                'data_vencimento' => $fatura->data_vencimento,
            ]);
        }

        foreach ($boletos as $boleto) {
            $fornecedor = $boleto->ordemCompra->fornecedor->pessoa->nome_social
                ?? $boleto->ordemCompra->fornecedor->pessoa->nome_registro
                ?? '-';
            $movimentacoes->push([
                'tipo' => 'Boleto',
                'referencia' => 'OC #' . $boleto->ordem_compra_id,
                'entidade' => $fornecedor,
                'valor' => $boleto->valor,
                'status' => $boleto->status,
                'data_vencimento' => $boleto->data_vencimento,
            ]);
        }

        $movimentacoes = $movimentacoes->sortByDesc('data_vencimento')->values();

        $clientes = Cliente::with('pessoa')->where('ativo', true)->get()
            ->sortBy(fn ($c) => $c->pessoa->nome_social ?? $c->pessoa->nome_registro);

        return view('relatorio.financeiro', compact(
            'dataInicio',
            'dataFim',
            'clienteId',
            'totalFaturas',
            'totalBoletos',
            'totalAReceber',
            'totalAPagar',
            'saldo',
            'movimentacoes',
            'clientes'
        ));
    }

    /**
     * Gera o relatório de movimentações de estoque.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function estoque(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->format('Y-m-d'));

        $totalMateriais = MateriaPrima::where('ativo', true)->count();

        $materiaisCriticos = MateriaPrima::where('ativo', true)
            ->where('aviso_estoque', true)
            ->whereColumn('estoque_atual', '<', 'estoque_minimo')
            ->count();

        $totalEntradas = Estoque::whereNotNull('entrada_id')
            ->whereBetween('data_movimentacao', [$dataInicio, $dataFim])
            ->sum('qtde');

        $totalSaidas = Estoque::whereNotNull('saida_id')
            ->whereBetween('data_movimentacao', [$dataInicio, $dataFim])
            ->sum('qtde');

        $totalPerdas = PerdaQuebra::whereBetween('data', [$dataInicio, $dataFim])
            ->sum('qtde');

        // Movimentações unificadas
        $entradas = Estoque::with('materiaPrima')
            ->whereNotNull('entrada_id')
            ->whereBetween('data_movimentacao', [$dataInicio, $dataFim])
            ->get()
            ->map(fn ($e) => [
                'tipo' => 'Entrada',
                'material' => $e->materiaPrima->descricao ?? '-',
                'qtde' => $e->qtde,
                'data' => $e->data_movimentacao,
            ]);

        $saidas = Estoque::with('materiaPrima')
            ->whereNotNull('saida_id')
            ->whereBetween('data_movimentacao', [$dataInicio, $dataFim])
            ->get()
            ->map(fn ($e) => [
                'tipo' => 'Saída',
                'material' => $e->materiaPrima->descricao ?? '-',
                'qtde' => $e->qtde,
                'data' => $e->data_movimentacao,
            ]);

        $perdas = PerdaQuebra::with('materiaPrima')
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->get()
            ->map(fn ($p) => [
                'tipo' => 'Perda',
                'material' => $p->materiaPrima->descricao ?? '-',
                'qtde' => $p->qtde,
                'data' => $p->data,
            ]);

        $movimentacoes = $entradas->concat($saidas)->concat($perdas)->sortByDesc('data')->values();

        return view('relatorio.estoque', compact(
            'dataInicio',
            'dataFim',
            'totalMateriais',
            'materiaisCriticos',
            'totalEntradas',
            'totalSaidas',
            'totalPerdas',
            'movimentacoes'
        ));
    }
}
