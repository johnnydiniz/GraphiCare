<?php

namespace App\Http\Controllers;

use App\Models\PerdaQuebra;
use App\Models\Estoque;
use App\Models\MateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller responsável pelo gerenciamento de perdas e quebras.
 *
 * @package App\Http\Controllers
 */
class PerdasQuebrasController extends Controller
{
    /**
     * Exibe a listagem de todas as perdas e quebras.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $perdasQuebras = PerdaQuebra::with('materiaPrima')->get();
        $title = 'Perdas e Quebras';
        $route = 'perda-quebra.salvar';
        return view('perda-quebra.index', compact('perdasQuebras', 'title', 'route'));
    }

    /**
     * Exibe o formulário para registrar uma nova perda ou quebra.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('perda-quebra.formulario', [
            'title' => 'Registrar Perda/Quebra',
            'route' => 'perda-quebra.salvar',
            'btn_label' => 'Registrar',
            'defaultDate' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Armazena um novo registro de perda/quebra e atualiza o estoque correspondente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Exibe o formulário para editar o registro de perda/quebra especificado.
     *
     * @param  \App\Models\PerdaQuebra  $perdaQuebra
     * @return \Illuminate\Contracts\View\View
     */
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

    /**
     * Atualiza o registro de perda/quebra especificado, revertendo e reaplicando os ajustes de estoque.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PerdaQuebra  $perdaQuebra
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Remove o registro de perda/quebra especificado e reverte a dedução do estoque.
     *
     * @param  \App\Models\PerdaQuebra  $perdaQuebra
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Obtém todas as matérias-primas ativas em JSON para preenchimento de dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
