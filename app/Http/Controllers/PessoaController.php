<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Funcionario;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pessoas = Pessoa::with(['fornecedor', 'cliente', 'funcionario'])->get();

        return view('pessoa.index', compact('pessoas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new Pessoa())->generateFields(__FUNCTION__);
        return view('pessoa.formulario', ['title' => 'Cadastrar Pessoa', 'route' => 'pessoa.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $temFuncionario = $temCliente = $temFornecedor = false;

        //Validação de campos específicos de pessoa
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

        //Validação de campos específicos de funcionário
        if (! empty($request->cargo) || ! empty($request->salario)) {
            $temFuncionario = true;
            $validator      = Validator::make($request->all(), [
                'cargo'   => 'string|max:255',
                'salario' => 'numeric',
            ]);

            if ($validator->fails()) {
                $fields = $this->generateSessionFields($request);
                return back()->with($fields)->withErrors($validator);
            }
        }

        //Validação de campos específicos de cliente
        if (! empty($request->tipo_cliente) && $request->tipo_cliente != 'nao_informado') {
            $temCliente = true;
            $validator  = Validator::make($request->all(), [
                'tipo_cliente'  => 'required|string|max:255|unique:pessoas',
                'cpf_cnpj'      => 'string|max:14|unique:pessoas',
                'nome_registro' => 'required|string|max:255',
                'senha'         => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                $fields = $this->generateSessionFields($request);
                return back()->with($fields)->withErrors($validator);
            }
        }

        //Validação de campos específicos de fornecedor
        if (! empty($request->tipo_cliente) && $request->tipo_cliente != 'nao_informado') {
            $temFornecedor = true;
            $validator     = Validator::make($request->all(), [
                'tipo_fornecedor' => 'required|string|max:255|unique:pessoas',
                'cpf_cnpj'        => 'string|max:14|unique:pessoas',
                'nome_registro'   => 'required|string|max:255',
                'senha'           => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                $fields = $this->generateSessionFields($request);
                return back()->with($fields)->withErrors($validator);
            }
        }

        DB::beginTransaction();

        try {
            $pessoa = Pessoa::create([
                'ativo'           => true,
                'tipo'            => $request->tipo,
                'login'           => $request->login,
                'senha'           => bcrypt($request->senha),
                'cpf_cnpj'        => $request->cpf_cnpj,
                'nome_registro'   => $request->nome_registro,
                'nome_social'     => $request->nome_social ?? $request->nome_registro,
                'data_nascimento' => $request->data_nascimento,
                'escolaridade'    => $request->escolaridade,
            ]);

            $temFuncionario ?? Funcionario::create([
                'pessoa_id' => $pessoa->id,
                'cargo'     => $request->cargo,
                'salario'   => $request->salario,
            ]);

            $temCliente ?? Cliente::create([
                'pessoa_id'      => $pessoa->id,
                'tipo'           => $request->tipo_cliente,
                'limite_credito' => $request->limite_credito,
                'taxa_desconto'  => $request->taxa_desconto,
            ]);

            $temFornecedor ?? Fornecedor::create([
                'pessoa_id' => $pessoa->id,
                'tipo'      => $request->tipo_fornecedor,
            ]);

            DB::commit();
            return redirect()->route('pessoa.index')->with('success', 'Funcionário cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pessoa $pessoa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pessoa $pessoa)
    {
        $fields = $pessoa->generateFields(__FUNCTION__);
        return view('pessoa.formulario', ['title' => 'Editar Funcionário', 'route' => ['pessoa.editar', $pessoa->id], 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pessoa $pessoa)
    {
        DB::beginTransaction();
        $pessoa = $pessoa->load('pessoa');

        try {

            $pessoa->pessoa->update([
                'nome_registro' => $request->nome_registro,
                'nome_social'   => $request->nome_social ?? $request->nome_registro,
                'login'         => $request->login,
                'senha'         => $request->senha ? bcrypt($request->senha) : $pessoa->pessoa->senha,
                'cpf_cnpj'      => $request->cpf_cnpj,
            ]);

            $pessoa->update([
                'cargo' => $request->cargo,
            ]);

            DB::commit();
            return redirect()->route('pessoa.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pessoa $pessoa)
    {
        DB::beginTransaction();
        try {
            if (! is_null($pessoa->cliente)) {
                $pessoa->cliente->delete();
            }
            if (! is_null($pessoa->fornecedor)) {
                $pessoa->fornecedor->delete();
            }
            if (! is_null($pessoa->funcionario)) {
                $pessoa->funcionario->delete();
            }
            $pessoa->delete();
            DB::commit();
            return redirect()->route('pessoa.index')->with('success', 'Pessoa excluída com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pessoa.index')->withErrors(['db_error' => 'Erro ao excluir pessoa: ' . $e->getMessage()]);
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
