<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\Orcamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\Saida;
use App\Models\Estoque;
use App\Models\Fatura;
use App\Models\PerdaQuebra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdemServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordensServico = OrdemServico::with(['cliente.pessoa', 'servicos', 'orcamento'])->get();
        $title = 'Orders';
        $route = 'ordem-servico.inserir';
        return view('ordem-servico.index', compact('ordensServico', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clientes = Cliente::with('pessoa')
            ->whereHas('pessoa', function($q) {
                $q->where('ativo', true);
            })
            ->where('ativo', true)
            ->get();

        $orcamentos = Orcamento::with(['cliente.pessoa', 'servicos'])->where('ativo', true)->get();

        $defaultDates = [
            'data_inicio' => now()->format('Y-m-d'),
        ];

        $orcamentoSelecionado = null;
        if ($request->has('orcamento_id')) {
            $orcamentoSelecionado = Orcamento::with(['cliente.pessoa', 'servicos'])->find($request->orcamento_id);
        }

        return view('ordem-servico.formulario', [
            'title' => __('Create Order'),
            'route' => 'ordem-servico.inserir',
            'btn_label' => __('Create'),
            'clientes' => $clientes,
            'orcamentos' => $orcamentos,
            'defaultDates' => $defaultDates,
            'orcamentoSelecionado' => $orcamentoSelecionado,
        ]);
    }

    /**
     * Store a newly created resource in storage.
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
            'tipo_entrega' => 'required|in:retirada,entrega',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'data_entrega' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'orcamento_id' => 'nullable|exists:orcamentos,id',
        ]);

        try {
            $custoFinal = 0;
            foreach ($request->servicos as $servicoData) {
                $servico = Servico::find($servicoData['id']);
                if ($servico) {
                    $custoFinal += $servico->custo_estimado * $servicoData['qtde'];
                }
            }

            $taxaLucro = $request->taxa_lucro ?? 0;
            $desconto = $request->desconto ?? 0;
            $valorComLucro = $custoFinal * (1 + ($taxaLucro / 100));
            $valorFinal = $valorComLucro * (1 - ($desconto / 100));

            $ordemServico = OrdemServico::create([
                'cliente_id' => $request->cliente_id,
                'orcamento_id' => $request->orcamento_id,
                'taxa_lucro' => $taxaLucro,
                'desconto' => $desconto,
                'custo_final' => $custoFinal,
                'valor_final' => $valorFinal,
                'tipo_entrega' => $request->tipo_entrega,
                'data_inicio' => $request->data_inicio,
                'data_fim' => $request->data_fim,
                'data_entrega' => $request->data_entrega,
                'observacoes' => $request->observacoes,
                'ativo' => true,
                'status' => 'pendente',
            ]);

            $servicosSync = [];
            foreach ($request->servicos as $servicoData) {
                $servicosSync[$servicoData['id']] = ['qtde' => $servicoData['qtde']];
            }
            $ordemServico->servicos()->sync($servicosSync);

            return redirect()->route('ordem-servico.index')->with('success', __('Order created successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => __('Error creating order: ') . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrdemServico $ordemServico)
    {
        $ordemServico->load(['servicos', 'cliente.pessoa', 'orcamento']);

        $clientes = Cliente::with('pessoa')
            ->whereHas('pessoa', function($q) {
                $q->where('ativo', true);
            })
            ->where('ativo', true)
            ->get();

        $orcamentos = Orcamento::with(['cliente.pessoa', 'servicos'])->where('ativo', true)->get();

        return view('ordem-servico.formulario', [
            'title' => __('Edit Order'),
            'route' => ['ordem-servico.editar', $ordemServico->id],
            'btn_label' => __('Save'),
            'ordemServico' => $ordemServico,
            'clientes' => $clientes,
            'orcamentos' => $orcamentos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrdemServico $ordemServico)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servicos' => 'required|array|min:1',
            'servicos.*.id' => 'required|exists:servicos,id',
            'servicos.*.qtde' => 'required|integer|min:1',
            'taxa_lucro' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0|max:100',
            'tipo_entrega' => 'required|in:retirada,entrega',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'data_entrega' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'orcamento_id' => 'nullable|exists:orcamentos,id',
        ]);

        try {
            $custoFinal = 0;
            foreach ($request->servicos as $servicoData) {
                $servico = Servico::find($servicoData['id']);
                if ($servico) {
                    $custoFinal += $servico->custo_estimado * $servicoData['qtde'];
                }
            }

            $taxaLucro = $request->taxa_lucro ?? 0;
            $desconto = $request->desconto ?? 0;
            $valorComLucro = $custoFinal * (1 + ($taxaLucro / 100));
            $valorFinal = $valorComLucro * (1 - ($desconto / 100));

            $ordemServico->update([
                'cliente_id' => $request->cliente_id,
                'orcamento_id' => $request->orcamento_id,
                'taxa_lucro' => $taxaLucro,
                'desconto' => $desconto,
                'custo_final' => $custoFinal,
                'valor_final' => $valorFinal,
                'tipo_entrega' => $request->tipo_entrega,
                'data_inicio' => $request->data_inicio,
                'data_fim' => $request->data_fim,
                'data_entrega' => $request->data_entrega,
                'observacoes' => $request->observacoes,
                'ativo' => $request->has('ativo'),
            ]);

            $servicosSync = [];
            foreach ($request->servicos as $servicoData) {
                $servicosSync[$servicoData['id']] = ['qtde' => $servicoData['qtde']];
            }
            $ordemServico->servicos()->sync($servicosSync);

            return redirect()->route('ordem-servico.index')->with('success', __('Order updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => __('Error updating order: ') . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(OrdemServico $ordemServico)
    {
        try {
            $ordemServico->update(['ativo' => !$ordemServico->ativo]);
            $status = $ordemServico->ativo ? __('activated') : __('deactivated');
            return redirect()->route('ordem-servico.index')->with('success', __('Order :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('ordem-servico.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrdemServico $ordemServico)
    {
        try {
            $ordemServico->servicos()->detach();
            $ordemServico->delete();
            return redirect()->route('ordem-servico.index')->with('success', __('Order deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('ordem-servico.index')->withErrors(['db_error' => __('Error deleting order: ') . $e->getMessage()]);
        }
    }

    /**
     * Print production view
     */
    public function printProducao(OrdemServico $ordemServico)
    {
        $ordemServico->load([
            'servicos.componenteServico.materiaprima.tipoMateriaPrima',
            'servicos.componenteServico.equipamentoOperacional',
            'cliente.pessoa',
        ]);
        return view('ordem-servico.print-producao', compact('ordemServico'));
    }

    /**
     * Print admin view
     */
    public function printAdmin(OrdemServico $ordemServico)
    {
        $ordemServico->load([
            'servicos.componenteServico.materiaprima.tipoMateriaPrima',
            'servicos.componenteServico.equipamentoOperacional',
            'cliente.pessoa',
            'orcamento',
        ]);
        return view('ordem-servico.print-admin', compact('ordemServico'));
    }

    /**
     * Get all active services for the dropdown (JSON)
     */
    public function getServicos()
    {
        $servicos = Servico::with(['componenteServico.materiaPrima'])->where('ativo', true)->get()->map(function($servico) {
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

    /**
     * Start an order (set status to em_andamento)
     */
    public function iniciar(OrdemServico $ordemServico)
    {
        try {
            if ($ordemServico->status !== 'pendente') {
                return redirect()->route('ordem-servico.index')->withErrors(['db_error' => __('Only pending orders can be started.')]);
            }

            $ordemServico->update([
                'status' => 'em_andamento',
                'data_inicio' => $ordemServico->data_inicio ?? now(),
            ]);

            return redirect()->route('ordem-servico.index')->with('success', __('Order started successfully.'));
        } catch (\Exception $e) {
            return redirect()->route('ordem-servico.index')->withErrors(['db_error' => __('Error starting order: ') . $e->getMessage()]);
        }
    }

    /**
     * Finish an order (set status to finalizada, deduct stock, record losses)
     */
    public function finalizar(Request $request, OrdemServico $ordemServico)
    {
        try {
            if ($ordemServico->status !== 'em_andamento') {
                return redirect()->route('ordem-servico.index')->withErrors(['db_error' => __('Only in-progress orders can be finished.')]);
            }

            DB::beginTransaction();

            $ordemServico->load(['servicos.componenteServico.materiaprima']);
            $perdas = $request->input('perdas', []);
            $hoje = now();

            foreach ($ordemServico->servicos as $servico) {
                $qtdeServico = $servico->pivot->qtde ?? 1;

                foreach ($servico->componenteServico as $componente) {
                    if ($componente->tipo !== 'material' || !$componente->materiaprima) {
                        continue;
                    }

                    $qtdeComponente = $componente->pivot->qtde ?? 1;
                    $qtdeUsada = $qtdeServico * $qtdeComponente;

                    // Get loss for this component in this OS
                    $qtdePerda = 0;
                    $chavePerda = $componente->id;
                    if (isset($perdas[$chavePerda]) && is_numeric($perdas[$chavePerda]) && $perdas[$chavePerda] > 0) {
                        $qtdePerda = (int) $perdas[$chavePerda];
                    }

                    $qtdeTotal = $qtdeUsada + $qtdePerda;

                    // Create saida record (stock exit)
                    $saida = Saida::create([
                        'qtde' => $qtdeTotal,
                        'data_inicio' => $ordemServico->data_inicio,
                        'data_termino' => $hoje,
                        'componente_servico_id' => $componente->id,
                        'ordem_servico_id' => $ordemServico->id,
                    ]);

                    // Create estoque movement
                    Estoque::create([
                        'qtde' => $qtdeTotal,
                        'materia_prima_id' => $componente->materiaprima->id,
                        'saida_id' => $saida->id,
                        'data_movimentacao' => $hoje,
                    ]);

                    // Deduct from materia prima stock
                    $componente->materiaprima->decrement('estoque_atual', $qtdeTotal);

                    // Record loss/breakage if any
                    if ($qtdePerda > 0) {
                        PerdaQuebra::create([
                            'qtde' => $qtdePerda,
                            'data' => $hoje,
                            'componente_servico_id' => $componente->id,
                            'ordem_servico_id' => $ordemServico->id,
                        ]);
                    }
                }
            }

            $ordemServico->update([
                'status' => 'finalizada',
                'data_fim' => $hoje,
            ]);

            Fatura::create([
                'ordem_servico_id' => $ordemServico->id,
                'valor' => $ordemServico->valor_final,
                'data_emissao' => $hoje,
                'data_vencimento' => $hoje->copy()->addDays(30),
                'status' => 'pendente',
            ]);

            DB::commit();

            return redirect()->route('ordem-servico.index')->with('success', __('Order finished successfully. Stock has been updated.') . ' Fatura gerada automaticamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ordem-servico.index')->withErrors(['db_error' => __('Error finishing order: ') . $e->getMessage()]);
        }
    }

    /**
     * Get material components for an order (JSON) - for the finalization modal
     */
    public function getComponentes(OrdemServico $ordemServico)
    {
        $ordemServico->load(['servicos.componenteServico.materiaprima']);

        $componentes = [];

        foreach ($ordemServico->servicos as $servico) {
            $qtdeServico = $servico->pivot->qtde ?? 1;

            foreach ($servico->componenteServico as $componente) {
                if ($componente->tipo !== 'material' || !$componente->materiaprima) {
                    continue;
                }

                $qtdeComponente = $componente->pivot->qtde ?? 1;
                $qtdeUsada = $qtdeServico * $qtdeComponente;

                // Group by component id
                if (isset($componentes[$componente->id])) {
                    $componentes[$componente->id]['qtde_usada'] += $qtdeUsada;
                } else {
                    $componentes[$componente->id] = [
                        'componente_id' => $componente->id,
                        'descricao' => $componente->descricao,
                        'materia_prima' => $componente->materiaprima->descricao,
                        'materia_prima_id' => $componente->materiaprima->id,
                        'estoque_atual' => $componente->materiaprima->estoque_atual,
                        'qtde_usada' => $qtdeUsada,
                    ];
                }
            }
        }

        return response()->json(array_values($componentes));
    }

    /**
     * Get services from a specific orcamento (JSON), with remaining quantities
     */
    public function getOrcamentoServicos($id)
    {
        $orcamento = Orcamento::with(['servicos.componenteServico.materiaPrima', 'cliente.pessoa', 'ordensServico.servicos'])->findOrFail($id);

        // Calculate consumed quantities per service across all OS of this orcamento
        $consumido = [];
        foreach ($orcamento->ordensServico as $os) {
            foreach ($os->servicos as $servico) {
                $consumido[$servico->id] = ($consumido[$servico->id] ?? 0) + ($servico->pivot->qtde ?? 1);
            }
        }

        $servicos = $orcamento->servicos->map(function($servico) use ($consumido) {
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

            $qtdeOrcamento = $servico->pivot->qtde ?? 1;
            $qtdeConsumida = $consumido[$servico->id] ?? 0;
            $qtdeRemanescente = max(0, $qtdeOrcamento - $qtdeConsumida);

            return [
                'id' => $servico->id,
                'descricao' => $servico->descricao,
                'custo_estimado' => $servico->custo_estimado,
                'qtde' => $qtdeRemanescente,
                'qtde_orcamento' => $qtdeOrcamento,
                'qtde_consumida' => $qtdeConsumida,
                'qtde_remanescente' => $qtdeRemanescente,
                'esgotado' => $qtdeRemanescente <= 0,
                'materiais' => $materiaisComEstoque,
            ];
        });

        $expirado = $orcamento->validade && $orcamento->validade->isPast();

        return response()->json([
            'cliente_id' => $orcamento->cliente_id,
            'cliente_nome' => $orcamento->cliente->pessoa->nome_social ?? $orcamento->cliente->pessoa->nome_registro ?? '-',
            'taxa_lucro' => $orcamento->taxa_lucro ?? 0,
            'desconto' => $orcamento->desconto ?? 0,
            'observacoes' => $orcamento->observacoes,
            'expirado' => $expirado,
            'validade' => $orcamento->validade ? $orcamento->validade->format('d/m/Y') : null,
            'servicos' => $servicos,
        ]);
    }
}
