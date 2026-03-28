<?php

namespace App\Http\Controllers;

use App\Models\PerdaQuebra;
use App\Models\Estoque;
use App\Models\MateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerdasQuebrasController extends Controller
{
    public function index()
    {
        $perdasQuebras = PerdaQuebra::with('materiaPrima')->get();
        $title = 'Perdas e Quebras';
        $route = 'perda-quebra.salvar';
        return view('perda-quebra.index', compact('perdasQuebras', 'title', 'route'));
    }

    public function create()
    {
        return view('perda-quebra.formulario', [
            'title' => 'Registrar Perda/Quebra',
            'route' => 'perda-quebra.salvar',
            'btn_label' => 'Registrar',
            'defaultDate' => now()->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_prima_id' => 'required|exists:materias_primas,id',
            'qtde' => 'required|integer|min:1',
            'motivo' => 'required|in:quebra,desperdicio,vencimento,ajuste,outro',
            'data' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $perdaQuebra = PerdaQuebra::create([
                'qtde' => $request->qtde,
                'data' => $request->data,
                'motivo' => $request->motivo,
                'observacoes' => $request->observacoes,
                'materia_prima_id' => $request->materia_prima_id,
            ]);

            Estoque::create([
                'qtde' => -$request->qtde,
                'materia_prima_id' => $request->materia_prima_id,
                'perda_quebra_id' => $perdaQuebra->id,
                'data_movimentacao' => $request->data,
            ]);

            $mp = MateriaPrima::findOrFail($request->materia_prima_id);
            $mp->update([
                'estoque_atual' => $mp->estoque_atual - $request->qtde,
            ]);

            DB::commit();

            return redirect()->route('perda-quebra.index')->with('success', 'Perda/Quebra registrada com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao registrar perda/quebra: ' . $e->getMessage()]);
        }
    }

    public function edit(PerdaQuebra $perdaQuebra)
    {
        $perdaQuebra->load('materiaPrima');

        return view('perda-quebra.formulario', [
            'title' => 'Editar Perda/Quebra',
            'route' => ['perda-quebra.atualizar', $perdaQuebra->id],
            'btn_label' => 'Salvar',
            'perdaQuebra' => $perdaQuebra,
        ]);
    }

    public function update(Request $request, PerdaQuebra $perdaQuebra)
    {
        $request->validate([
            'materia_prima_id' => 'required|exists:materias_primas,id',
            'qtde' => 'required|integer|min:1',
            'motivo' => 'required|in:quebra,desperdicio,vencimento,ajuste,outro',
            'data' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Reverte estoque anterior
            $mpAntiga = MateriaPrima::findOrFail($perdaQuebra->materia_prima_id);
            $mpAntiga->update([
                'estoque_atual' => $mpAntiga->estoque_atual + $perdaQuebra->qtde,
            ]);

            // Atualiza a perda/quebra
            $perdaQuebra->update([
                'qtde' => $request->qtde,
                'data' => $request->data,
                'motivo' => $request->motivo,
                'observacoes' => $request->observacoes,
                'materia_prima_id' => $request->materia_prima_id,
            ]);

            // Atualiza registro de estoque
            $estoque = Estoque::where('perda_quebra_id', $perdaQuebra->id)->first();
            if ($estoque) {
                $estoque->update([
                    'qtde' => -$request->qtde,
                    'materia_prima_id' => $request->materia_prima_id,
                    'data_movimentacao' => $request->data,
                ]);
            }

            // Aplica novo decremento
            $mpNova = MateriaPrima::findOrFail($request->materia_prima_id);
            $mpNova->update([
                'estoque_atual' => $mpNova->estoque_atual - $request->qtde,
            ]);

            DB::commit();

            return redirect()->route('perda-quebra.index')->with('success', 'Perda/Quebra atualizada com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao atualizar perda/quebra: ' . $e->getMessage()]);
        }
    }

    public function destroy(PerdaQuebra $perdaQuebra)
    {
        try {
            DB::beginTransaction();

            // Reverte estoque
            $mp = MateriaPrima::findOrFail($perdaQuebra->materia_prima_id);
            $mp->update([
                'estoque_atual' => $mp->estoque_atual + $perdaQuebra->qtde,
            ]);

            // Deleta registro de estoque associado
            Estoque::where('perda_quebra_id', $perdaQuebra->id)->delete();

            // Deleta perda/quebra
            $perdaQuebra->delete();

            DB::commit();

            return redirect()->route('perda-quebra.index')->with('success', 'Perda/Quebra excluída com sucesso. Estoque revertido.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('perda-quebra.index')->withErrors(['db_error' => 'Erro ao excluir perda/quebra: ' . $e->getMessage()]);
        }
    }

    public function getMateriasPrimas()
    {
        $materiasPrimas = MateriaPrima::where('ativo', true)->get()->map(function ($mp) {
            return [
                'id' => $mp->id,
                'descricao' => $mp->descricao,
                'estoque_atual' => $mp->estoque_atual,
            ];
        });

        return response()->json($materiasPrimas);
    }
}
