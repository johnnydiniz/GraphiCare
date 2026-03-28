<?php
namespace App\Http\Controllers;

use App\Models\ComponenteServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComponenteServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $componentesServico = ComponenteServico::with(['materiaPrima.tipoMateriaPrima', 'equipamentoOperacional'])->get();
        $title = 'Service Components';
        $route = 'componente-servico.salvar';

        return view('componente-servico.index', compact('componentesServico', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new ComponenteServico())->generateFields(__FUNCTION__);
        return view('componente-servico.formulario', ['title' => 'Cadastrar Componente de serviço', 'route' => 'componente-servico.salvar', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'tipo'              => 'required',
            'descricao'         => 'required',
            'materia_prima_id'  => 'nullable',
            'equipamento_operacional_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {

            ComponenteServico::create([
                'ativo'             => true,
                'tipo'              => $request->tipo,
                'descricao'         => $request->descricao,
                'materia_prima_id'  => $request->materia_prima_id,
                'equipamento_operacional_id' => $request->equipamento_operacional_id,
            ]);

            DB::commit();
            return redirect()->route('componente-servico.index')->with('success', 'Componente de serviço cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar componente de serviço: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ComponenteServico $componenteServico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ComponenteServico $componenteServico)
    {
        $fields = $componenteServico->generateFields(__FUNCTION__);
        return view('componente-servico.formulario', ['title' => 'Editar Componente de serviço', 'route' => ['componente-servico.atualizar', $componenteServico->id], 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComponenteServico $componenteServico)
    {
        DB::beginTransaction();

        try {

            $componenteServico->update([
                'tipo'              => $request->tipo,
                'descricao'         => $request->descricao,
                'materia_prima_id'  => $request->materia_prima_id,
                'equipamento_operacional_id' => $request->equipamento_operacional_id,
            ]);

            DB::commit();
            return redirect()->route('componente-servico.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar componente de serviço: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(ComponenteServico $componenteServico)
    {
        try {
            $componenteServico->update(['ativo' => !$componenteServico->ativo]);
            $status = $componenteServico->ativo ? __('activated') : __('deactivated');
            return redirect()->route('componente-servico.index')->with('success', __('Service component :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('componente-servico.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComponenteServico $componenteServico)
    {
        DB::beginTransaction();
        try {
            $componenteServico->delete();
            DB::commit();
            return redirect()->route('componente-servico.index')->with('success', 'Componente de serviço excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('componente-servico.index')->withErrors(['db_error' => 'Erro ao excluir componente de serviço: ' . $e->getMessage()]);
        }
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'tipo'              => $request->tipo,
            'descricao'         => $request->descricao,
            'materia_prima_id'  => $request->materia_prima_id,
            'equipamento_operacional_id' => $request->equipamento_operacional_id,
        ];
        return $fields;
    }

    /**
     * Get active components by type (AJAX)
     */
    public function getByType(Request $request)
    {
        $tipo = $request->get('tipo');

        $componentes = ComponenteServico::with(['materiaPrima', 'equipamentoOperacional'])
            ->where('ativo', true)
            ->where('tipo', $tipo)
            ->get()
            ->map(function ($componente) {
                // Custo base vem do custo_operacional do componente (preenchido automaticamente)
                $custoBase = $componente->custo_operacional ?? 0;

                return [
                    'id' => $componente->id,
                    'descricao' => $componente->descricao,
                    'custo_base' => $custoBase,
                ];
            });

        return response()->json($componentes);
    }
}
