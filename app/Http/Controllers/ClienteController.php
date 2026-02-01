<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::with('pessoa')->get();

        return view('cliente.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new Cliente())->generateFields(__FUNCTION__);
        return view('cliente.formulario', ['title' => 'Cadastrar Cliente', 'route' => 'cliente.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
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

            Cliente::create([
                'ativo'         => true,
                'pessoa_id'     => $pessoa->id,
                'tipo'          => $request->tipo_cliente,
                'limite_credito' => $request->limite_credito,
                'taxa_desconto' => $request->taxa_desconto,
            ]);

            DB::commit();
            return redirect()->route('cliente.index')->with('success', 'Cliente cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar cliente: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $fields = $cliente->generateFields(__FUNCTION__);
        return view('cliente.formulario', ['title' => 'Editar Cliente', 'route' => ['cliente.editar', $cliente->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        DB::beginTransaction();
        $cliente = $cliente->load('pessoa');

        try {

            $cliente->pessoa->update([
                'nome_registro' => $request->nome_registro,
                'nome_social'   => $request->nome_social ?? $request->nome_registro,
                'login'         => $request->login,
                'senha'         => $request->senha ? bcrypt($request->senha) : $cliente->pessoa->senha,
                'cpf_cnpj'      => $request->cpf_cnpj,
            ]);

            $cliente->update([
                'cargo' => $request->cargo,
            ]);

            DB::commit();
            return redirect()->route('cliente.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar cliente: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(Cliente $cliente)
    {
        try {
            $cliente->update(['ativo' => !$cliente->ativo]);
            $status = $cliente->ativo ? __('activated') : __('deactivated');
            return redirect()->back()->with('success', __('Customer :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        DB::beginTransaction();
        try {
            $cliente->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Cliente excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['db_error' => 'Erro ao excluir cliente: ' . $e->getMessage()]);
        }
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'nome_registro' => $request->nome_registro,
            'nome_social'   => $request->nome_social ?? null,
            'login'         => $request->login,
            'cpf_cnpj'      => $request->cpf_cnpj,
            'tipo_cliente'         => $request->tipo_cliente,
        ];
        return $fields;
    }
}
