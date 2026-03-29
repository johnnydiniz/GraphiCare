<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use App\Models\Cliente;
use App\Models\Servico;
use Illuminate\Http\Request;

/**
 * Controller responsável pelo gerenciamento de orçamentos.
 *
 * @package App\Http\Controllers
 */
class OrcamentoController extends Controller
{
    /**
     * Exibe a listagem de todos os orçamentos.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $orcamentos = Orcamento::with(['cliente.pessoa', 'servicos', 'ordensServico.servicos'])->get();
        $title = 'Quotes';
        $route = 'orcamento.salvar';
        return view('orcamento.index', compact('orcamentos', 'title', 'route'));
    }

    /**
     * Exibe o formulário para criar um novo orçamento.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $clientes = Cliente::with('pessoa')
            ->whereHas('pessoa', function($q) {
                $q->where('ativo', true);
            })
            ->where('ativo', true)
            ->get();

        // Default dates: today for previsao_inicio, today + 15 days for validade
        $defaultDates = [
            'previsao_inicio' => now()->format('Y-m-d'),
            'validade' => now()->addDays(15)->format('Y-m-d'),
        ];

        return view('orcamento.formulario', [
            'title' => __('Create Quote'),
            'route' => 'orcamento.salvar',
            'btn_label' => __('Create'),
            'clientes' => $clientes,
            'defaultDates' => $defaultDates,
        ]);
    }

    /**
     * Armazena um novo orçamento com seus serviços associados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servicos' => 'required|array|min:1',
            'servicos.*.id' => 'required|exists:servicos,id',
            'servicos.*.qtde' => 'required|integer|min:1',
            'taxa_lucro' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0|max:100',
            'previsao_inicio' => 'nullable|date',
            'previsao_entrega' => 'nullable|date|after_or_equal:previsao_inicio',
            'validade' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            // Calculate custo_final from services
            $custoFinal = 0;
            foreach ($request->servicos as $servicoData) {
                $servico = Servico::find($servicoData['id']);
                if ($servico) {
                    $custoFinal += $servico->custo_estimado * $servicoData['qtde'];
                }
            }

            // Calculate valor_final
            $taxaLucro = $request->taxa_lucro ?? 0;
            $desconto = $request->desconto ?? 0;
            $valorComLucro = $custoFinal * (1 + ($taxaLucro / 100));
            $valorFinal = $valorComLucro * (1 - ($desconto / 100));

            $orcamento = Orcamento::create([
                'cliente_id' => $request->cliente_id,
                'taxa_lucro' => $taxaLucro,
                'desconto' => $desconto,
                'custo_final' => $custoFinal,
                'valor_final' => $valorFinal,
                'previsao_inicio' => $request->previsao_inicio,
                'previsao_entrega' => $request->previsao_entrega,
                'validade' => $request->validade,
                'observacoes' => $request->observacoes,
                'ativo' => true,
            ]);

            // Attach services with quantities
            $servicosSync = [];
            foreach ($request->servicos as $servicoData) {
                $servicosSync[$servicoData['id']] = ['qtde' => $servicoData['qtde']];
            }
            $orcamento->servicos()->sync($servicosSync);

            return redirect()->route('orcamento.index')->with('success', __('Quote created successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => __('Error creating quote: ') . $e->getMessage()]);
        }
    }

    /**
     * Exibe o orçamento especificado.
     *
     * @param  \App\Models\Orcamento  $orcamento
     * @return void
     */
    public function show(Orcamento $orcamento)
    {
        //
    }

    /**
     * Exibe o formulário para editar o orçamento especificado.
     *
     * @param  \App\Models\Orcamento  $orcamento
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Orcamento $orcamento)
    {
        $orcamento->load(['servicos', 'cliente.pessoa', 'ordensServico.servicos']);

        $clientes = Cliente::with('pessoa')
            ->whereHas('pessoa', function($q) {
                $q->where('ativo', true);
            })
            ->where('ativo', true)
            ->get();

        return view('orcamento.formulario', [
            'title' => __('Edit Quote'),
            'route' => ['orcamento.atualizar', $orcamento->id],
            'btn_label' => __('Save'),
            'orcamento' => $orcamento,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Atualiza o orçamento especificado e sincroniza seus serviços associados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orcamento  $orcamento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Orcamento $orcamento)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servicos' => 'required|array|min:1',
            'servicos.*.id' => 'required|exists:servicos,id',
            'servicos.*.qtde' => 'required|integer|min:1',
            'taxa_lucro' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0|max:100',
            'previsao_inicio' => 'nullable|date',
            'previsao_entrega' => 'nullable|date|after_or_equal:previsao_inicio',
            'validade' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            // Calculate custo_final from services
            $custoFinal = 0;
            foreach ($request->servicos as $servicoData) {
                $servico = Servico::find($servicoData['id']);
                if ($servico) {
                    $custoFinal += $servico->custo_estimado * $servicoData['qtde'];
                }
            }

            // Calculate valor_final
            $taxaLucro = $request->taxa_lucro ?? 0;
            $desconto = $request->desconto ?? 0;
            $valorComLucro = $custoFinal * (1 + ($taxaLucro / 100));
            $valorFinal = $valorComLucro * (1 - ($desconto / 100));

            $orcamento->update([
                'cliente_id' => $request->cliente_id,
                'taxa_lucro' => $taxaLucro,
                'desconto' => $desconto,
                'custo_final' => $custoFinal,
                'valor_final' => $valorFinal,
                'previsao_inicio' => $request->previsao_inicio,
                'previsao_entrega' => $request->previsao_entrega,
                'validade' => $request->validade,
                'observacoes' => $request->observacoes,
                'ativo' => $request->has('ativo'),
            ]);

            // Sync services with quantities
            $servicosSync = [];
            foreach ($request->servicos as $servicoData) {
                $servicosSync[$servicoData['id']] = ['qtde' => $servicoData['qtde']];
            }
            $orcamento->servicos()->sync($servicosSync);

            return redirect()->route('orcamento.index')->with('success', __('Quote updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => __('Error updating quote: ') . $e->getMessage()]);
        }
    }

    /**
     * Alterna o status ativo do orçamento especificado.
     *
     * @param  \App\Models\Orcamento  $orcamento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Orcamento $orcamento)
    {
        try {
            $orcamento->update(['ativo' => !$orcamento->ativo]);
            $status = $orcamento->ativo ? __('activated') : __('deactivated');
            return redirect()->route('orcamento.index')->with('success', __('Quote :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('orcamento.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove o orçamento especificado e desvincula seus serviços.
     *
     * @param  \App\Models\Orcamento  $orcamento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Orcamento $orcamento)
    {
        try {
            $orcamento->servicos()->detach();
            $orcamento->delete();
            return redirect()->route('orcamento.index')->with('success', __('Quote deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('orcamento.index')->withErrors(['db_error' => __('Error deleting quote: ') . $e->getMessage()]);
        }
    }

    /**
     * Exibe a visualização imprimível do orçamento especificado para o cliente.
     *
     * @param  \App\Models\Orcamento  $orcamento
     * @return \Illuminate\Contracts\View\View
     */
    public function printCliente(Orcamento $orcamento)
    {
        $orcamento->load(['servicos', 'cliente.pessoa']);
        return view('orcamento.print-cliente', compact('orcamento'));
    }

    /**
     * Exibe a visualização imprimível administrativa do orçamento especificado com detalhes completos dos componentes.
     *
     * @param  \App\Models\Orcamento  $orcamento
     * @return \Illuminate\Contracts\View\View
     */
    public function printAdmin(Orcamento $orcamento)
    {
        $orcamento->load([
            'servicos.componenteServico.materiaprima.tipoMateriaPrima',
            'servicos.componenteServico.equipamentoOperacional',
            'cliente.pessoa',
            'ordensServico',
        ]);
        return view('orcamento.print-admin', compact('orcamento'));
    }

    /**
     * Obtém todos os serviços ativos com dados de disponibilidade de estoque em JSON para preenchimento de dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServicos()
    {
        $servicos = Servico::with(['componenteServico.materiaPrima'])->where('ativo', true)->get()->map(function($servico) {
            // Check stock availability for materials
            $materiaisComEstoque = [];
            foreach ($servico->componenteServico as $componente) {
                if ($componente->tipo == 'material' && $componente->materiaPrima) {
                    $qtdeNecessaria = $componente->pivot->qtde ?? 1;
                    $estoqueAtual = $componente->materiaPrima->estoque_atual ?? 0;
                    $materiaisComEstoque[] = [
                        'material_id' => $componente->materiaPrima->id,
                        'descricao' => $componente->descricao,
                        'qtde_por_servico' => $qtdeNecessaria,
                        'estoque_atual' => $estoqueAtual,
                        'max_execucoes' => $qtdeNecessaria > 0 ? floor($estoqueAtual / $qtdeNecessaria) : 0,
                    ];
                }
            }

            return [
                'id' => $servico->id,
                'descricao' => $servico->descricao,
                'custo_estimado' => $servico->custo_estimado,
                'materiais' => $materiaisComEstoque,
            ];
        });

        return response()->json($servicos);
    }
}
