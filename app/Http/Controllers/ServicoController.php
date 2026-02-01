<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicos = Servico::with('componenteServico.materiaPrima')->get();
        $title = 'Services';
        $route = 'servico.inserir';

        return view('servico.index', compact('servicos', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('servico.formulario', [
            'title' => __('Register Service'),
            'route' => 'servico.inserir',
            'btn_label' => __('Insert'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Servico $servico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servico $servico)
    {
        $servico->load('componenteServico.materiaPrima');

        return view('servico.formulario', [
            'title' => __('Edit Service'),
            'route' => ['servico.editar', $servico->id],
            'btn_label' => __('Save'),
            'servico' => $servico,
        ]);
    }

    /**
     * Update the specified resource in storage.
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
     * Toggle the active status of the specified resource.
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
     * Remove the specified resource from storage.
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
