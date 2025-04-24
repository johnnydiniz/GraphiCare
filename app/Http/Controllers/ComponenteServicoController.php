<?php
namespace App\Http\Controllers;

use App\Models\ComponenteServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use NumberFormatter;

class ComponenteServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
        $componentesServico = ComponenteServico::with('MateriaPrima')->get();

        return view('componente-servico.index', compact('componentesServico', 'formatter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new ComponenteServico())->generateFields(__FUNCTION__);
        return view('componente-servico.formulario', ['title' => 'Cadastrar Componente de serviço', 'route' => 'componente-servico.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'tipo'              => 'required',
            'descricao'         => 'required',
            'qtde'              => 'required',
            'materia_prima_id'  => 'required',
            'custo_operacional' => 'required',
        ]);
        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {

            ComponenteServico::create([
                'tipo'              => $request->tipo,
                'descricao'         => $request->descricao,
                'qtde'              => $request->qtde,
                'materia_prima_id'  => $request->materia_prima_id,
                'custo_operacional' => $request->custo_operacional,
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
        return view('componente-servico.formulario', ['title' => 'Editar Componente de serviço', 'route' => ['componente-servico.editar', $componenteServico->id], 'fields' => $fields, 'btn_label' => 'Salvar']);
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
                'qtde'              => $request->qtde,
                'materia_prima_id'  => $request->materia_prima_id,
                'custo_operacional' => $request->custo_operacional,
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
            'qtde'              => $request->qtde,
            'materia_prima_id'  => $request->materia_prima_id,
            'custo_operacional' => $request->custo_operacional,
        ];
        return $fields;
    }
}
