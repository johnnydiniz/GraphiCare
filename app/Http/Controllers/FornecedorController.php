<?php
namespace App\Http\Controllers;

use App\Models\Fornecedor;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FornecedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fornecedores = Fornecedor::with('pessoa')->get();

        return view('fornecedor.index', compact('fornecedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new Fornecedor())->generateFields(__FUNCTION__);
        return view('fornecedor.formulario', ['title' => 'Cadastrar Fornecedor', 'route' => 'fornecedor.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'         => 'required|string|max:255|unique:pessoas',
            'cpf_cnpj'      => 'string|max:14|unique:pessoas',
            'nome_registro' => 'required|string|max:255',
            'senha'         => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $pessoa = Pessoa::create([
                'ativo'         => true,
                'tipo'          => $request->tipo,
                'login'         => $request->login,
                'senha'         => bcrypt($request->senha),
                'cpf_cnpj'      => $request->cpf_cnpj,
                'nome_registro' => $request->nome_registro,
                'nome_social'   => $request->nome_social ?? $request->nome_registro,
                'data_nascimento' => $request->data_nascimento,
                'escolaridade'  => $request->escolaridade,
            ]);

            Fornecedor::create([
                'pessoa_id' => $pessoa->id,
                'tipo'      => $request->tipo_fornecedor,
            ]);

            DB::commit();
            return redirect()->route('fornecedor.index')->with('success', 'Fornecedor cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Fornecedor $fornecedor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fornecedor $fornecedor)
    {
        $fields = $fornecedor->generateFields(__FUNCTION__);
        return view('fornecedor.formulario', ['title' => 'Editar Fornecedor', 'route' => ['fornecedor.editar', $fornecedor->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fornecedor $fornecedor)
    {
        DB::beginTransaction();
        $fornecedor = $fornecedor->load('pessoa');

        try {

            $fornecedor->pessoa->update([
                'nome_registro' => $request->nome_registro,
                'nome_social'   => $request->nome_social ?? $request->nome_registro,
                'login'         => $request->login,
                'senha'         => $request->senha ? bcrypt($request->senha) : $fornecedor->pessoa->senha,
                'cpf_cnpj'      => $request->cpf_cnpj,
            ]);

            $fornecedor->update([
                'tipo' => $request->tipo_fornecedor,
            ]);

            DB::commit();
            return redirect()->route('fornecedor.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fornecedor $fornecedor)
    {
        DB::beginTransaction();
        try {
            $fornecedor->delete();
            DB::commit();
            return redirect()->route('fornecedor.index')->with('success', 'Fornecedor excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('fornecedor.index')->withErrors(['db_error' => 'Erro ao excluir funcionário: ' . $e->getMessage()]);
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
