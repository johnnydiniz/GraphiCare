<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Fatura;
use App\Models\MateriaPrima;
use App\Models\OrdemServico;
use Carbon\Carbon;

/**
 * Controller responsável pelo painel de controle e página inicial da aplicação.
 *
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Cria uma nova instância do controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe o painel de controle da aplicação com KPIs, resumo financeiro,
     * ordens recentes, níveis críticos de estoque e vencimentos próximos.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $hoje = Carbon::today();
        $inicioMes = $hoje->copy()->startOfMonth();
        $fimMes = $hoje->copy()->endOfMonth();
        $em7dias = $hoje->copy()->addDays(7);

        // KPI Cards
        $totalOrdens = OrdemServico::where('ativo', true)->count();
        $ordensPendentes = OrdemServico::where('ativo', true)->where('status', 'pendente')->count();
        $receitaMes = Fatura::where('status', 'paga')
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes])
            ->sum('valor');
        $estoqueCritico = MateriaPrima::where('ativo', true)
            ->where('aviso_estoque', true)
            ->whereNotNull('estoque_minimo')
            ->whereColumn('estoque_atual', '<', 'estoque_minimo')
            ->count();

        // Resumo financeiro
        $totalAReceber = Fatura::whereIn('status', ['pendente', 'vencida'])->sum('valor');
        $totalRecebido = Fatura::where('status', 'paga')->sum('valor');
        $totalAPagar = Boleto::whereIn('status', ['pendente', 'vencido'])->sum('valor');
        $totalPago = Boleto::where('status', 'pago')->sum('valor');
        $saldo = $totalRecebido - $totalPago;

        // Últimas 5 ordens de serviço
        $ordensRecentes = OrdemServico::with('cliente.pessoa')
            ->where('ativo', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Materiais com estoque abaixo do mínimo
        $materiaisCriticos = MateriaPrima::with('tipoMateriaPrima')
            ->where('ativo', true)
            ->where('aviso_estoque', true)
            ->whereNotNull('estoque_minimo')
            ->whereColumn('estoque_atual', '<', 'estoque_minimo')
            ->orderByRaw('estoque_atual - estoque_minimo asc')
            ->take(5)
            ->get();

        // Vencimentos próximos (7 dias) + vencidos
        $vencimentos = collect();

        $faturasPendentes = Fatura::with('ordemServico.cliente.pessoa')
            ->where('status', 'pendente')
            ->where('data_vencimento', '<=', $em7dias)
            ->orderBy('data_vencimento')
            ->get();

        foreach ($faturasPendentes as $fatura) {
            $vencido = $fatura->data_vencimento->lt($hoje);
            $cliente = optional(optional(optional($fatura->ordemServico)->cliente)->pessoa);
            $vencimentos->push([
                'tipo' => 'Fatura',
                'icone' => 'fa-file-invoice-dollar',
                'referencia' => 'OS #' . $fatura->ordem_servico_id,
                'entidade' => $cliente->nome_social ?? $cliente->nome_registro ?? '-',
                'valor' => $fatura->valor,
                'data_vencimento' => $fatura->data_vencimento,
                'vencido' => $vencido,
                'dias' => $vencido ? 0 : $hoje->diffInDays($fatura->data_vencimento),
            ]);
        }

        $boletosPendentes = Boleto::with('ordemCompra.fornecedor.pessoa')
            ->where('status', 'pendente')
            ->where('data_vencimento', '<=', $em7dias)
            ->orderBy('data_vencimento')
            ->get();

        foreach ($boletosPendentes as $boleto) {
            $vencido = $boleto->data_vencimento->lt($hoje);
            $fornecedor = optional(optional(optional($boleto->ordemCompra)->fornecedor)->pessoa);
            $vencimentos->push([
                'tipo' => 'Boleto',
                'icone' => 'fa-barcode',
                'referencia' => 'OC #' . $boleto->ordem_compra_id,
                'entidade' => $fornecedor->nome_social ?? $fornecedor->nome_registro ?? '-',
                'valor' => $boleto->valor,
                'data_vencimento' => $boleto->data_vencimento,
                'vencido' => $vencido,
                'dias' => $vencido ? 0 : $hoje->diffInDays($boleto->data_vencimento),
            ]);
        }

        $vencimentos = $vencimentos->sortBy('data_vencimento')->values();

        return view('home', compact(
            'totalOrdens',
            'ordensPendentes',
            'receitaMes',
            'estoqueCritico',
            'totalAReceber',
            'totalRecebido',
            'totalAPagar',
            'totalPago',
            'saldo',
            'ordensRecentes',
            'materiaisCriticos',
            'vencimentos'
        ));
    }
}
