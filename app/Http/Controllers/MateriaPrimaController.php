<?php
namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use NumberFormatter;

class MateriaPrimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
        $materiasPrimas = MateriaPrima::with('tipoMateriaPrima')->get();

        return view('materia-prima.index', compact('materiasPrimas', 'formatter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new MateriaPrima())->generateFields(__FUNCTION__);
        return view('materia-prima.formulario', ['title' => 'Cadastrar Matéria-prima', 'route' => 'materia-prima.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|unique:materias_primas',
            'custo_medio' => 'required|numeric',
            'estoque_atual' => 'required|numeric',
            'estoque_minimo' => 'nullable|numeric',
            'tipo' => 'required|exists:tipo_materias_primas,id',
        ]);
        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {

            MateriaPrima::create([
                'ativo' => true,
                'descricao' => $request->descricao,
                'custo_medio' => $request->custo_medio ?? 0,
                'estoque_atual' => $request->estoque_atual ?? 0,
                'estoque_minimo' => $request->estoque_minimo ?? null,
                'aviso_estoque' => $request->aviso_estoque ? true : false,
                'tipo_materia_prima_id' => $request->tipo,
            ]);

            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Matéria-prima cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar matéria-prima: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MateriaPrima $materiaPrima)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MateriaPrima $materiaPrima)
    {
        $fields = $materiaPrima->generateFields(__FUNCTION__);
        return view('materia-prima.formulario', ['title' => 'Editar Matéria-prima', 'route' => ['materia-prima.editar', $materiaPrima->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MateriaPrima $materiaPrima)
    {
        DB::beginTransaction();

        try {

            $materiaPrima->update([
                'descricao' => $request->descricao,
                'custo_medio' => $request->custo_medio,
                'estoque_atual' => $request->estoque_atual,
                'estoque_minimo' => $request->estoque_minimo,
                'aviso_estoque' => $request->aviso_estoque ? true : false,
                'tipo_materia_prima_id' => $request->tipo,
            ]);

            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar matéria-prima: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MateriaPrima $materiaPrima)
    {
        DB::beginTransaction();
        try {
            $materiaPrima->delete();
            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Matéria-prima excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('materia-prima.index')->withErrors(['db_error' => 'Erro ao excluir matéria-prima: ' . $e->getMessage()]);
        }
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'tipo' => $request->tipo,
            'descricao' => $request->descricao,
            'custo_medio' => $request->custo_medio,
            'estoque_atual' => $request->estoque_atual,
            'estoque_minimo' => $request->estoque_minimo,
            'aviso_estoque' => $request->aviso_estoque,
        ];
        return $fields;
    }
}
