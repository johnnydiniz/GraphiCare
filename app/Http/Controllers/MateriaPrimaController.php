<?php
namespace App\Http\Controllers;

use App\Models\MateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MateriaPrimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materiasPrimas = MateriaPrima::all();

        return view('materia-prima.index', compact('materiasPrimas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new MateriaPrima())->generateFields(__FUNCTION__);
        return view('materia-prima.formulario', ['title' => 'Cadastrar Funcionário', 'route' => 'materia-prima.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
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
                'aviso_estoque' => $request->aviso_estoque ?? false,
                'tipo_materia_prima_id' => $request->tipo,
            ]);

            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Funcionário cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
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
        return view('materia-prima.formulario', ['title' => 'Editar Funcionário', 'route' => ['materia-prima.editar', $materiaPrima->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MateriaPrima $materiaPrima)
    {
        DB::beginTransaction();
        $materiaPrima = $materiaPrima->load('pessoa');

        try {

            $materiaPrima->pessoa->update([
                'nome_registro' => $request->nome_registro,
                'nome_social'   => $request->nome_social ?? $request->nome_registro,
                'login'         => $request->login,
                'senha'         => $request->senha ? bcrypt($request->senha) : $materiaPrima->pessoa->senha,
                'cpf_cnpj'      => $request->cpf_cnpj,
            ]);

            $materiaPrima->update([
                'cargo' => $request->cargo,
            ]);

            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MateriaPrima $materiaPrima)
    {
        DB::beginTransaction();
        try {
            $materiaPrima = $materiaPrima->load('pessoa');
            $materiaPrima->delete();
            $materiaPrima->pessoa->delete();
            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Funcionário excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('materia-prima.index')->withErrors(['db_error' => 'Erro ao excluir funcionário: ' . $e->getMessage()]);
        }
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'nome_registro' => $request->nome_registro,
            'nome_social'   => $request->nome_social ?? null,
            'login'         => $request->login,
            'cpf_cnpj'      => $request->cpf_cnpj,
            'cargo'         => $request->cargo,
        ];
        return $fields;
    }
}
