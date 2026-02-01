<?php

namespace App\Http\Controllers;

use App\Models\ComponenteServico;
use App\Models\EquipamentoOperacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EquipamentoOperacionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $equipamentosOperacionais = EquipamentoOperacional::all();
        $title = 'Operational Equipments';
        $route = 'equipamento-operacional.inserir';

        return view('equipamento-operacional.index', compact('equipamentosOperacionais', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new EquipamentoOperacional())->generateFields(__FUNCTION__);
        return view('equipamento-operacional.formulario', ['title' => 'Cadastrar Equipamento Operacional', 'route' => 'equipamento-operacional.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|unique:equipamentos_operacionais',
            'custo' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $equipamentoOperacional = EquipamentoOperacional::create([
                'ativo' => true,
                'descricao' => $request->descricao,
                'custo' => $request->custo ?? 0,
            ]);

            // Auto-criar ComponenteServico
            ComponenteServico::create([
                'ativo' => true,
                'tipo' => 'equipamento',
                'descricao' => $equipamentoOperacional->descricao,
                'materia_prima_id' => null,
                'equipamento_operacional_id' => $equipamentoOperacional->id,
                'custo_operacional' => $equipamentoOperacional->custo,
            ]);

            DB::commit();
            return redirect()->route('equipamento-operacional.index')->with('success', 'Equipamento operacional cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar equipamento operacional: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipamentoOperacional $equipamentoOperacional)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipamentoOperacional $equipamentoOperacional)
    {
        $fields = $equipamentoOperacional->generateFields(__FUNCTION__);
        return view('equipamento-operacional.formulario', ['title' => 'Editar Equipamento Operacional', 'route' => ['equipamento-operacional.editar', $equipamentoOperacional->id], 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EquipamentoOperacional $equipamentoOperacional)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|unique:equipamentos_operacionais,descricao,' . $equipamentoOperacional->id,
            'custo' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $equipamentoOperacional->update([
                'ativo' => $request->has('ativo'),
                'descricao' => $request->descricao,
                'custo' => $request->custo,
            ]);

            // Atualizar o ComponenteServico associado
            ComponenteServico::where('equipamento_operacional_id', $equipamentoOperacional->id)->update([
                'descricao' => $request->descricao,
                'custo_operacional' => $request->custo,
            ]);

            DB::commit();
            return redirect()->route('equipamento-operacional.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao atualizar equipamento operacional: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(EquipamentoOperacional $equipamentoOperacional)
    {
        try {
            $equipamentoOperacional->update(['ativo' => !$equipamentoOperacional->ativo]);
            $status = $equipamentoOperacional->ativo ? __('activated') : __('deactivated');
            return redirect()->route('equipamento-operacional.index')->with('success', __('Operational equipment :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('equipamento-operacional.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipamentoOperacional $equipamentoOperacional)
    {
        DB::beginTransaction();
        try {
            $equipamentoOperacional->delete();
            DB::commit();
            return redirect()->route('equipamento-operacional.index')->with('success', 'Equipamento operacional excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('equipamento-operacional.index')->withErrors(['db_error' => 'Erro ao excluir equipamento operacional: ' . $e->getMessage()]);
        }
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'descricao' => $request->descricao,
            'custo' => $request->custo,
        ];
        return $fields;
    }
}
