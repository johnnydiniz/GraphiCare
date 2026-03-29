<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador responsável pelo gerenciamento de serviços.
 *
 * @package App\Http\Controllers
 */
class ServicoController extends Controller
{
    /**
     * Exibe a listagem do recurso.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $servicos = Servico::with('componenteServico.materiaPrima')->get();
        $title = 'Services';
        $route = 'servico.salvar';

        return view('servico.index', compact('servicos', 'title', 'route'));
    }

    /**
     * Exibe o formulário para criação de um novo recurso.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('servico.formulario', [
            'title' => __('Register Service'),
            'route' => 'servico.salvar',
            'btn_label' => __('Insert'),
        ]);
    }

    /**
     * Armazena um recurso recém-criado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|unique:servicos',
            'componentes' => 'required|array|min:1',
            'componentes.*.id' => 'required|exists:componente_servicos,id',
        ], [
            'descricao.required' => __('The description field is required.'),
            'descricao.unique' => __('This description is already registered.'),
            'componentes.required' => __('You must add at least one component.'),
            'componentes.min' => __('You must add at least one component.'),
            'componentes.*.id.required' => __('Select a component for each row.'),
            'componentes.*.id.exists' => __('The selected component is invalid.'),
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $servico = Servico::create([
                'ativo' => true,
                'descricao' => $request->descricao,
            ]);

            // Attach components with order, quantity and operational cost
            $componentes = [];
            $ordem = 1;
            foreach ($request->componentes as $componente) {
                if (!empty($componente['id'])) {
                    $componentes[$componente['id']] = [
                        'ordem' => $ordem,
                        'qtde' => $componente['qtde'] ?? 1,
                        'custo_operacional' => $componente['custo_operacional'] ?? 0,
                    ];
                    $ordem++;
                }
            }
            $servico->componenteServico()->attach($componentes);

            DB::commit();
            return redirect()->route('servico.index')->with('success', __('Service registered successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['db_error' => __('Error registering service: ') . $e->getMessage()]);
        }
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param  \App\Models\Servico  $servico
     * @return void
     */
    public function show(Servico $servico)
    {
        //
    }

    /**
     * Exibe o formulário para edição do recurso especificado.
     *
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Servico $servico)
    {
        $servico->load('componenteServico.materiaPrima');

        return view('servico.formulario', [
            'title' => __('Edit Service'),
            'route' => ['servico.atualizar', $servico->id],
            'btn_label' => __('Save'),
            'servico' => $servico,
        ]);
    }

    /**
     * Atualiza o recurso especificado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Servico $servico)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|unique:servicos,descricao,' . $servico->id,
            'componentes' => 'required|array|min:1',
            'componentes.*.id' => 'required|exists:componente_servicos,id',
        ], [
            'descricao.required' => __('The description field is required.'),
            'descricao.unique' => __('This description is already registered.'),
            'componentes.required' => __('You must add at least one component.'),
            'componentes.min' => __('You must add at least one component.'),
            'componentes.*.id.required' => __('Select a component for each row.'),
            'componentes.*.id.exists' => __('The selected component is invalid.'),
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $servico->update([
                'ativo' => $request->has('ativo'),
                'descricao' => $request->descricao,
            ]);

            // Sync components with order, quantity and operational cost
            $componentes = [];
            $ordem = 1;
            foreach ($request->componentes as $componente) {
                if (!empty($componente['id'])) {
                    $componentes[$componente['id']] = [
                        'ordem' => $ordem,
                        'qtde' => $componente['qtde'] ?? 1,
                        'custo_operacional' => $componente['custo_operacional'] ?? 0,
                    ];
                    $ordem++;
                }
            }
            $servico->componenteServico()->sync($componentes);

            DB::commit();
            return redirect()->route('servico.index')->with('success', __('Changes saved successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['db_error' => __('Error updating service: ') . $e->getMessage()]);
        }
    }

    /**
     * Alterna o status ativo do recurso especificado.
     *
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Servico $servico)
    {
        try {
            $servico->update(['ativo' => !$servico->ativo]);
            $status = $servico->ativo ? __('activated') : __('deactivated');
            return redirect()->route('servico.index')->with('success', __('Service :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('servico.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove o recurso especificado do banco de dados.
     *
     * @param  \App\Models\Servico  $servico
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Servico $servico)
    {
        DB::beginTransaction();
        try {
            $servico->componenteServico()->detach();
            $servico->delete();
            DB::commit();
            return redirect()->route('servico.index')->with('success', __('Service deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('servico.index')->withErrors(['db_error' => __('Error deleting service: ') . $e->getMessage()]);
        }
    }
}
