<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\OrdemCompra;
use App\Models\Entrada;
use App\Models\Estoque;
use App\Models\Fornecedor;
use App\Models\MateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdemCompraController extends Controller
{
    public function index()
    {
        $ordensCompra = OrdemCompra::with(['fornecedor.pessoa', 'entradas.materiaPrima'])->get();
        $title = 'Ordens de Compra';
        $route = 'ordem-compra.salvar';
        return view('ordem-compra.index', compact('ordensCompra', 'title', 'route'));
    }

    public function create()
    {
        $fornecedores = Fornecedor::with('pessoa')
            ->whereHas('pessoa', function ($q) {
                $q->where('ativo', true);
            })
            ->where('ativo', true)
            ->get();

        $defaultDates = [
            'data_emissao' => now()->format('Y-m-d'),
        ];

        return view('ordem-compra.formulario', [
            'title' => 'Criar Ordem de Compra',
            'route' => 'ordem-compra.salvar',
            'btn_label' => 'Criar',
            'fornecedores' => $fornecedores,
            'defaultDates' => $defaultDates,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'nota_fiscal' => 'nullable|string|max:255',
            'data_emissao' => 'required|date',
            'data_entrega' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.materia_prima_id' => 'required|exists:materias_primas,id',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
            'itens.*.qtde' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $valorTotal = 0;
            foreach ($request->itens as $item) {
                $valorTotal += $item['valor_unitario'] * $item['qtde'];
            }

            $ordemCompra = OrdemCompra::create([
                'fornecedor_id' => $request->fornecedor_id,
                'nota_fiscal' => $request->nota_fiscal,
                'data_emissao' => $request->data_emissao,
                'data_entrega' => $request->data_entrega,
                'observacoes' => $request->observacoes,
                'valor_total' => $valorTotal,
                'ativo' => true,
                'status' => 'pendente',
            ]);

            foreach ($request->itens as $item) {
                Entrada::create([
                    'qtde' => $item['qtde'],
                    'valor_unitario' => $item['valor_unitario'],
                    'materia_prima_id' => $item['materia_prima_id'],
                    'ordem_compra_id' => $ordemCompra->id,
                ]);
            }

            DB::commit();

            return redirect()->route('ordem-compra.index')->with('success', 'Ordem de compra criada com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao criar ordem de compra: ' . $e->getMessage()]);
        }
    }

    public function edit(OrdemCompra $oc)
    {
        if ($oc->status === 'recebida') {
            return redirect()->route('ordem-compra.index')->withErrors(['db_error' => 'Ordens recebidas não podem ser editadas.']);
        }

        $oc->load(['entradas.materiaPrima', 'fornecedor.pessoa']);

        $fornecedores = Fornecedor::with('pessoa')
            ->whereHas('pessoa', function ($q) {
                $q->where('ativo', true);
            })
            ->where('ativo', true)
            ->get();

        return view('ordem-compra.formulario', [
            'title' => 'Editar Ordem de Compra',
            'route' => ['ordem-compra.atualizar', $oc->id],
            'btn_label' => 'Salvar',
            'ordemCompra' => $oc,
            'fornecedores' => $fornecedores,
        ]);
    }

    public function update(Request $request, OrdemCompra $oc)
    {
        if ($oc->status === 'recebida') {
            return redirect()->route('ordem-compra.index')->withErrors(['db_error' => 'Ordens recebidas não podem ser editadas.']);
        }

        $request->validate([
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'nota_fiscal' => 'nullable|string|max:255',
            'data_emissao' => 'required|date',
            'data_entrega' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.materia_prima_id' => 'required|exists:materias_primas,id',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
            'itens.*.qtde' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $valorTotal = 0;
            foreach ($request->itens as $item) {
                $valorTotal += $item['valor_unitario'] * $item['qtde'];
            }

            $oc->update([
                'fornecedor_id' => $request->fornecedor_id,
                'nota_fiscal' => $request->nota_fiscal,
                'data_emissao' => $request->data_emissao,
                'data_entrega' => $request->data_entrega,
                'observacoes' => $request->observacoes,
                'valor_total' => $valorTotal,
            ]);

            $oc->entradas()->delete();

            foreach ($request->itens as $item) {
                Entrada::create([
                    'qtde' => $item['qtde'],
                    'valor_unitario' => $item['valor_unitario'],
                    'materia_prima_id' => $item['materia_prima_id'],
                    'ordem_compra_id' => $oc->id,
                ]);
            }

            DB::commit();

            return redirect()->route('ordem-compra.index')->with('success', 'Ordem de compra atualizada com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao atualizar ordem de compra: ' . $e->getMessage()]);
        }
    }

    public function destroy(OrdemCompra $oc)
    {
        if ($oc->status === 'recebida') {
            return redirect()->route('ordem-compra.index')->withErrors(['db_error' => 'Ordens recebidas não podem ser excluídas.']);
        }

        try {
            $oc->entradas()->delete();
            $oc->delete();
            return redirect()->route('ordem-compra.index')->with('success', 'Ordem de compra excluída com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('ordem-compra.index')->withErrors(['db_error' => 'Erro ao excluir ordem de compra: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus(OrdemCompra $oc)
    {
        try {
            $oc->update(['ativo' => !$oc->ativo]);
            $status = $oc->ativo ? 'ativada' : 'desativada';
            return redirect()->route('ordem-compra.index')->with('success', 'Ordem de compra ' . $status . ' com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('ordem-compra.index')->withErrors(['db_error' => 'Erro ao alterar status: ' . $e->getMessage()]);
        }
    }

    public function getMateriasPrimas()
    {
        $materiasPrimas = MateriaPrima::where('ativo', true)->get()->map(function ($mp) {
            return [
                'id' => $mp->id,
                'descricao' => $mp->descricao,
                'custo_medio' => $mp->custo_medio,
                'estoque_atual' => $mp->estoque_atual,
            ];
        });

        return response()->json($materiasPrimas);
    }

    public function receber(Request $request, OrdemCompra $oc)
    {
        if ($oc->status === 'recebida') {
            return redirect()->route('ordem-compra.index')->withErrors(['db_error' => 'Esta ordem já foi recebida.']);
        }

        $request->validate([
            'boleto_data_emissao' => 'required|date',
            'boleto_data_vencimento' => 'required|date',
            'boleto_observacoes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oc->load('entradas.materiaPrima');
            $hoje = now();

            foreach ($oc->entradas as $entrada) {
                Estoque::create([
                    'qtde' => $entrada->qtde,
                    'materia_prima_id' => $entrada->materia_prima_id,
                    'entrada_id' => $entrada->id,
                    'data_movimentacao' => $hoje,
                ]);

                $mp = $entrada->materiaPrima;
                $estoqueAnterior = $mp->estoque_atual;
                $custoAnterior = $mp->custo_medio;
                $qtdeNova = $entrada->qtde;
                $precoNovo = $entrada->valor_unitario;

                $totalQtde = $estoqueAnterior + $qtdeNova;
                if ($totalQtde > 0) {
                    $custoMedio = (($estoqueAnterior * $custoAnterior) + ($qtdeNova * $precoNovo)) / $totalQtde;
                } else {
                    $custoMedio = $precoNovo;
                }

                $mp->update([
                    'estoque_atual' => $totalQtde,
                    'custo_medio' => round($custoMedio, 2),
                ]);
            }

            $oc->update([
                'status' => 'recebida',
                'data_entrega' => $oc->data_entrega ?? $hoje,
            ]);

            Boleto::create([
                'ordem_compra_id' => $oc->id,
                'valor' => $oc->valor_total,
                'data_emissao' => $request->boleto_data_emissao,
                'data_vencimento' => $request->boleto_data_vencimento,
                'observacoes' => $request->boleto_observacoes,
                'status' => 'pendente',
            ]);

            DB::commit();

            return redirect()->route('ordem-compra.index')->with('success', 'Ordem de compra recebida com sucesso. O estoque foi atualizado e o boleto foi gerado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ordem-compra.index')->withErrors(['db_error' => 'Erro ao receber ordem de compra: ' . $e->getMessage()]);
        }
    }
}
