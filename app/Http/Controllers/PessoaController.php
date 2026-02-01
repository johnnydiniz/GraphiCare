<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contato;
use App\Models\Endereco;
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
        $title = 'People';
        $route = 'pessoa.inserir';
        return view('pessoa.index', compact('pessoas', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $function = __FUNCTION__;

        $fieldGroups = [
            ['key' => 'geral', 'label' => 'General', 'fields' => (new Pessoa())->generateFields($function)],
            ['key' => 'cliente', 'label' => 'Customer', 'fields' => (new Cliente())->generateFields($function)],
            ['key' => 'fornecedor', 'label' => 'Provider', 'fields' => (new Fornecedor())->generateFields($function)],
            ['key' => 'funcionario', 'label' => 'Employee', 'fields' => (new Funcionario())->generateFields($function)],
            ['key' => 'contato', 'label' => 'Contact', 'fields' => (new Contato())->generateFields($function)],
            ['key' => 'endereco', 'label' => 'Address', 'fields' => (new Endereco())->generateFields($function)],
        ];

        return view('pessoa.formulario', [
            'title' => 'Cadastrar Pessoa',
            'route' => 'pessoa.inserir',
            'fieldGroups' => $fieldGroups,
            'btn_label' => 'Cadastrar',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $temFuncionario = $temCliente = $temFornecedor = $temContato = $temEndereco = false;

        //Validação de campos específicos de pessoa
        $validator = Validator::make($request->all(), [
            'tipo'            => 'required|in:fisica,juridica',
            'login'           => 'required|string|max:255|unique:pessoas',
            'senha'           => 'required|string|min:8',
            'cpf_cnpj'        => 'required|string|max:14|unique:pessoas',
            'nome_registro'   => 'required|string|max:255',
            'nome_social'     => 'nullable|string|max:255',
            'data_nascimento' => 'nullable|date',
            'escolaridade'    => 'required|in:nao_informado,fundamental,medio,superior,pos_graduacao,mestrado,doutorado',
        ]);

        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        //Validação de campos específicos de contato
        if (! empty($request->tipo_contato) && ! empty($request->contato)) {
            $temContato = true;
            $validator  = Validator::make($request->all(), [
                'tipo_contato' => 'required|string|max:255',
                'contato'      => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                $fields = $this->generateSessionFields($request);
                return back()->with($fields)->withErrors($validator);
            }
        }

        //Validação de campos específicos de endereço
        if (! empty($request->cep)) {
            $temEndereco = true;
            $validator   = Validator::make($request->all(), [
                'cep'         => 'required|string|max:8',
                'logradouro'  => 'required|string|max:255',
                'numero'      => 'required|string|max:255',
                'complemento' => 'string|max:255',
                'bairro'      => 'required|string|max:255',
                'cidade'      => 'required|string|max:255',
                'estado'      => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                $fields = $this->generateSessionFields($request);
                return back()->with($fields)->withErrors($validator);
            }
        }

        //Validação de campos específicos de fornecedor
        if (! empty($request->tipo_fornecedor) && $request->tipo_fornecedor != 'nao_informado') {
            $temFornecedor = true;
            $validator     = Validator::make($request->all(), [
                'tipo_fornecedor' => 'in:produtos,servicos,produtos_servicos',
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
                'tipo_cliente'   => 'in:final,representante',
                'limite_credito' => 'numeric',
                'taxa_desconto'  => 'numeric',
            ]);

            if ($validator->fails()) {
                $fields = $this->generateSessionFields($request);
                return back()->with($fields)->withErrors($validator);
            }
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

            if ($temContato) {
                Contato::create([
                    'pessoa_id'    => $pessoa->id,
                    'tipo_contato' => $request->tipo_contato,
                    'contato'      => $request->contato,
                ]);
            }

            if ($temEndereco) {
                Endereco::create([
                    'pessoa_id'   => $pessoa->id,
                    'cep'         => $request->cep,
                    'logradouro'  => $request->logradouro,
                    'numero'      => $request->numero,
                    'complemento' => $request->complemento,
                    'bairro'      => $request->bairro,
                    'cidade'      => $request->cidade,
                    'estado'      => $request->estado,
                ]);
            }

            if ($temFornecedor) {
                Fornecedor::create([
                    'ativo'     => true,
                    'pessoa_id' => $pessoa->id,
                    'tipo'      => $request->tipo_fornecedor,
                ]);
            }

            if ($temCliente) {
                Cliente::create([
                    'ativo'          => true,
                    'pessoa_id'      => $pessoa->id,
                    'tipo'           => $request->tipo_cliente,
                    'limite_credito' => $request->limite_credito,
                    'taxa_desconto'  => $request->taxa_desconto,
                ]);
            }

            if ($temFuncionario) {
                Funcionario::create([
                    'ativo'     => true,
                    'pessoa_id' => $pessoa->id,
                    'cargo'     => $request->cargo,
                    'salario'   => $request->salario,
                ]);
            }

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
        $function = __FUNCTION__;

        $fieldGroups = [
            ['key' => 'geral', 'label' => 'General', 'fields' => $pessoa->generateFields($function)],
            ['key' => 'cliente', 'label' => 'Customer', 'fields' => ($pessoa->cliente ?? new Cliente())->generateFields($function)],
            ['key' => 'fornecedor', 'label' => 'Provider', 'fields' => ($pessoa->fornecedor ?? new Fornecedor())->generateFields($function)],
            ['key' => 'funcionario', 'label' => 'Employee', 'fields' => ($pessoa->funcionario ?? new Funcionario())->generateFields($function)],
            ['key' => 'contato', 'label' => 'Contact', 'fields' => ($pessoa->contatos && $pessoa->contatos->count() > 0
                ? $pessoa->contatos->first()
                : new Contato()
            )->generateFields($function)],
            ['key' => 'endereco', 'label' => 'Address', 'fields' => ($pessoa->endereco ?? new Endereco())->generateFields($function)],
        ];

        return view('pessoa.formulario', [
            'title' => 'Editar Pessoa',
            'route' => ['pessoa.editar', $pessoa->id],
            'fieldGroups' => $fieldGroups,
            'btn_label' => 'Salvar',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pessoa $pessoa)
    {

        DB::beginTransaction();

        try {

            $pessoa->update([
                'ativo'           => $request->ativo,
                'tipo'            => $request->tipo,
                'login'           => $request->login,
                'senha'           => $request->senha ? bcrypt($request->senha) : $pessoa->senha,
                'cpf_cnpj'        => $request->cpf_cnpj,
                'nome_registro'   => $request->nome_registro,
                'nome_social'     => $request->nome_social ?? $request->nome_registro,
                'data_nascimento' => $request->data_nascimento,
                'escolaridade'    => $request->escolaridade,
            ]);

            if (! empty($request->tipo_contato) && ! empty($request->contato)) {
                ! is_null($pessoa->contatos) ? $pessoa->contatos->each->update([
                    'tipo_contato' => $request->tipo_contato,
                    'contato'      => $request->contato,
                ]) : Contato::create([
                    'pessoa_id'    => $pessoa->id,
                    'tipo_contato' => $request->tipo_contato,
                    'contato'      => $request->contato,
                ]);
            }

            if (! empty($request->cep)) {
                ! is_null($pessoa->enderecos) ? $pessoa->enderecos->each->update([
                    'cep'         => $request->cep,
                    'logradouro'  => $request->logradouro,
                    'numero'      => $request->numero,
                    'complemento' => $request->complemento,
                    'bairro'      => $request->bairro,
                    'cidade'      => $request->cidade,
                    'estado'      => $request->estado,
                ]) : Endereco::create([
                    'pessoa_id'   => $pessoa->id,
                    'cep'         => $request->cep,
                    'logradouro'  => $request->logradouro,
                    'numero'      => $request->numero,
                    'complemento' => $request->complemento,
                    'bairro'      => $request->bairro,
                    'cidade'      => $request->cidade,
                    'estado'      => $request->estado,
                ]);
            }

            if (! empty($request->tipo_fornecedor) && $request->tipo_fornecedor != 'nao_informado') {
                ! is_null($pessoa->fornecedor) ? $pessoa->fornecedor->update([
                    'tipo' => $request->tipo_fornecedor,
                ]) : Fornecedor::create([
                    'ativo'     => true,
                    'pessoa_id' => $pessoa->id,
                    'tipo'      => $request->tipo_fornecedor,
                ]);
            }

            if (! empty($request->tipo_cliente) && $request->tipo_cliente != 'nao_informado') {
                ! is_null($pessoa->cliente) ? $pessoa->cliente->update([
                    'tipo'           => $request->tipo_cliente,
                    'limite_credito' => $request->limite_credito,
                    'taxa_desconto'  => $request->taxa_desconto,
                ]) : Cliente::create([
                    'ativo'          => true,
                    'pessoa_id'      => $pessoa->id,
                    'tipo'           => $request->tipo_cliente,
                    'limite_credito' => $request->limite_credito,
                    'taxa_desconto'  => $request->taxa_desconto,
                ]);
            }

            if (! empty($request->cargo) || ! empty($request->salario)) {
                ! is_null($pessoa->funcionario) ? $pessoa->funcionario->update([
                    'cargo'   => $request->cargo,
                    'salario' => $request->salario,
                ]) : Funcionario::create([
                    'ativo'     => true,
                    'pessoa_id' => $pessoa->id,
                    'cargo'     => $request->cargo,
                    'salario'   => $request->salario,
                ]);
            }

            DB::commit();
            return redirect()->route('pessoa.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(Pessoa $pessoa)
    {
        try {
            $pessoa->update(['ativo' => !$pessoa->ativo]);
            $status = $pessoa->ativo ? __('activated') : __('deactivated');
            return redirect()->route('pessoa.index')->with('success', __('Person :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('pessoa.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
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
            if (! is_null($pessoa->contatos)) {
                $pessoa->contatos->each->delete();
            }
            if (! is_null($pessoa->enderecos)) {
                $pessoa->enderecos->each->delete();
            }
            $pessoa->delete();
            DB::commit();
            return redirect()->route('pessoa.index')->with('success', 'Pessoa excluída com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pessoa.index')->withErrors(['db_error' => 'Erro ao excluir pessoa: ' . $e->getMessage()]);
        }
    }

    private function getFormTabs(): array
    {
        return [
            ['key' => 'geral', 'label' => 'General', 'icon' => 'fa-solid fa-users'],
            ['key' => 'cliente', 'label' => 'Customer', 'icon' => 'fa-solid fa-handshake'],
            ['key' => 'fornecedor', 'label' => 'Provider', 'icon' => 'fa-solid fa-truck'],
            ['key' => 'funcionario', 'label' => 'Employee', 'icon' => 'fa-solid fa-id-badge'],
            ['key' => 'contato', 'label' => 'Contact', 'icon' => 'fa-solid fa-address-book'],
            ['key' => 'endereco', 'label' => 'Address', 'icon' => 'fa-solid fa-location-dot'],
        ];
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'tipo'            => $request->tipo,
            'login'           => $request->login,
            'cpf_cnpj'        => $request->cpf_cnpj,
            'nome_registro'   => $request->nome_registro,
            'nome_social'     => $request->nome_social ?? null,
            'data_nascimento' => $request->data_nascimento,
            'escolaridade'    => $request->escolaridade,
            'cep'             => $request->cep,
            'logradouro'      => $request->logradouro,
            'numero'          => $request->numero,
            'complemento'     => $request->complemento,
            'bairro'          => $request->bairro,
            'cidade'          => $request->cidade,
            'estado'          => $request->estado,
            'tipo_fornecedor' => $request->tipo_fornecedor,
            'tipo_cliente'    => $request->tipo_cliente,
            'limite_credito'  => $request->limite_credito,
            'taxa_desconto'   => $request->taxa_desconto,
            'cargo'           => $request->cargo,
            'salario'         => $request->salario,
        ];

        return $fields;
    }
}
