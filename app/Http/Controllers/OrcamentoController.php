<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use App\Models\Cliente;
use App\Models\Servico;
use Illuminate\Http\Request;

class OrcamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orcamentos = Orcamento::with(['cliente.pessoa', 'servicos'])->get();
        $title = 'Quotes';
        $route = 'orcamento.inserir';
        return view('orcamento.index', compact('orcamentos', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
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
            'route' => 'orcamento.inserir',
            'btn_label' => __('Create'),
            'clientes' => $clientes,
            'defaultDates' => $defaultDates,
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
     * Display the specified resource.
     */
    public function show(Orcamento $orcamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orcamento $orcamento)
    {
        $orcamento->load(['servicos', 'cliente.pessoa']);

        $clientes = Cliente::with('pessoa')
            ->whereHas('pessoa', function($q) {
                $q->where('ativo', true);
            })
            ->where('ativo', true)
            ->get();

        return view('orcamento.formulario', [
            'title' => __('Edit Quote'),
            'route' => ['orcamento.editar', $orcamento->id],
            'btn_label' => __('Save'),
            'orcamento' => $orcamento,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Update the specified resource in storage.
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
     * Toggle the active status of the specified resource.
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
     * Remove the specified resource from storage.
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
     * Get all active services for the dropdown
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
