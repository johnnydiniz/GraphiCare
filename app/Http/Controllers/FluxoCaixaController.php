<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use App\Models\Boleto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FluxoCaixaController extends Controller
{
    public function index(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->format('Y-m-d'));

        $faturas = Fatura::with('ordemServico.cliente.pessoa')
            ->whereBetween('data_vencimento', [$dataInicio, $dataFim])
            ->get();

        $boletos = Boleto::with('ordemCompra.fornecedor.pessoa')
            ->whereBetween('data_vencimento', [$dataInicio, $dataFim])
            ->get();

        // KPIs
        $totalAReceber = $faturas->whereIn('status', ['pendente', 'vencida'])->sum('valor');
        $totalRecebido = $faturas->where('status', 'paga')->sum('valor');
        $totalAPagar = $boletos->whereIn('status', ['pendente', 'vencido'])->sum('valor');
        $totalPago = $boletos->where('status', 'pago')->sum('valor');
        $saldo = $totalRecebido - $totalPago;

        // Agrupar por mês para o gráfico
        $receitasPorMes = $faturas->where('status', 'paga')
            ->groupBy(fn ($f) => $f->data_vencimento->format('Y-m'))
            ->map(fn ($group) => $group->sum('valor'));

        $despesasPorMes = $boletos->where('status', 'pago')
            ->groupBy(fn ($b) => $b->data_vencimento->format('Y-m'))
            ->map(fn ($group) => $group->sum('valor'));

        // Gerar todos os meses no período
        $inicio = Carbon::parse($dataInicio)->startOfMonth();
        $fim = Carbon::parse($dataFim)->endOfMonth();
        $meses = [];
        $receitasData = [];
        $despesasData = [];

        while ($inicio->lte($fim)) {
            $key = $inicio->format('Y-m');
            $meses[] = $inicio->translatedFormat('M/Y');
            $receitasData[] = $receitasPorMes->get($key, 0);
            $despesasData[] = $despesasPorMes->get($key, 0);
            $inicio->addMonth();
        }

        // Movimentações recentes (faturas + boletos unificados)
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

        $movimentacoes = $movimentacoes->sortByDesc('data_vencimento')->take(20)->values();

        return view('fluxo-caixa.index', compact(
            'dataInicio',
            'dataFim',
            'totalAReceber',
            'totalRecebido',
            'totalAPagar',
            'totalPago',
            'saldo',
            'meses',
            'receitasData',
            'despesasData',
            'movimentacoes'
        ));
    }
}
