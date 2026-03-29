<?php

namespace App\Http\Controllers;

use App\Models\ComponenteServico;
use App\Models\EquipamentoOperacional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Gerencia operações CRUD e ações relacionadas a equipamentos operacionais.
 *
 * @package App\Http\Controllers
 */
class EquipamentoOperacionalController extends Controller
{
    /**
     * Exibe a listagem de equipamentos operacionais.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $equipamentosOperacionais = EquipamentoOperacional::all();
        $title = 'Operational Equipments';
        $route = 'equipamento-operacional.salvar';

        return view('equipamento-operacional.index', compact('equipamentosOperacionais', 'title', 'route'));
    }

    /**
     * Exibe o formulário para criação de um novo equipamento operacional.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $fields = (new EquipamentoOperacional())->generateFields(__FUNCTION__);
        return view('equipamento-operacional.formulario', ['title' => 'Cadastrar Equipamento Operacional', 'route' => 'equipamento-operacional.salvar', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Armazena um novo equipamento operacional no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
     * Exibe os detalhes de um equipamento operacional específico.
     *
     * @param  \App\Models\EquipamentoOperacional  $equipamentoOperacional
     * @return void
     */
    public function show(EquipamentoOperacional $equipamentoOperacional)
    {
        //
    }

    /**
     * Exibe o formulário de edição de um equipamento operacional existente.
     *
     * @param  \App\Models\EquipamentoOperacional  $equipamentoOperacional
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(EquipamentoOperacional $equipamentoOperacional)
    {
        $fields = $equipamentoOperacional->generateFields(__FUNCTION__);
        return view('equipamento-operacional.formulario', ['title' => 'Editar Equipamento Operacional', 'route' => ['equipamento-operacional.atualizar', $equipamentoOperacional->id], 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Atualiza um equipamento operacional existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EquipamentoOperacional  $equipamentoOperacional
     * @return \Illuminate\Http\RedirectResponse
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
     * Alterna o status ativo/inativo de um equipamento operacional.
     *
     * @param  \App\Models\EquipamentoOperacional  $equipamentoOperacional
     * @return \Illuminate\Http\RedirectResponse
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
     * Remove um equipamento operacional do banco de dados.
     *
     * @param  \App\Models\EquipamentoOperacional  $equipamentoOperacional
     * @return \Illuminate\Http\RedirectResponse
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

    /**
     * Gera os campos de sessão a partir da requisição para reexibição do formulário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function generateSessionFields(Request $request)
    {
        $fields = [
            'descricao' => $request->descricao,
            'custo' => $request->custo,
        ];
        return $fields;
    }
}
