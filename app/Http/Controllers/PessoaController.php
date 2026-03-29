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

/**
 * Controller responsável pelo gerenciamento de pessoas e seus papéis relacionados.
 *
 * @package App\Http\Controllers
 */
class PessoaController extends Controller
{
    /**
     * Exibe a listagem de todas as pessoas com seus papéis associados.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $pessoas = Pessoa::with(['fornecedor', 'cliente', 'funcionario'])->get();
        $title = 'People';
        $route = 'pessoa.salvar';
        return view('pessoa.index', compact('pessoas', 'title', 'route'));
    }

    /**
     * Exibe o formulário para cadastrar uma nova pessoa com abas de campos agrupados.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $function = __FUNCTION__;

        $fieldGroups = [
            ['key' => 'geral', 'label' => 'Geral', 'fields' => (new Pessoa())->generateFields($function)],
            ['key' => 'cliente', 'label' => 'Cliente', 'fields' => (new Cliente())->generateFields($function)],
            ['key' => 'fornecedor', 'label' => 'Fornecedor', 'fields' => (new Fornecedor())->generateFields($function)],
            ['key' => 'funcionario', 'label' => 'Funcionário', 'fields' => (new Funcionario())->generateFields($function)],
            ['key' => 'contato', 'label' => 'Contato', 'fields' => (new Contato())->generateFields($function)],
            ['key' => 'endereco', 'label' => 'Endereço', 'fields' => (new Endereco())->generateFields($function)],
        ];

        return view('pessoa.formulario', [
            'title' => 'Cadastrar Pessoa',
            'route' => 'pessoa.salvar',
            'fieldGroups' => $fieldGroups,
            'btn_label' => 'Cadastrar',
        ]);
    }

    /**
     * Armazena uma nova pessoa juntamente com registros opcionais de contato, endereço e papéis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
                'tipo_contato' => 'required|integer|exists:tipo_contatos,id',
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
                    'pessoa_id'      => $pessoa->id,
                    'tipo_contato_id' => $request->tipo_contato,
                    'contato'        => $request->contato,
                ]);
            }

            if ($temEndereco) {
                $endereco = Endereco::create([
                    'cep'         => $request->cep,
                    'logradouro'  => $request->logradouro,
                    'numero'      => $request->numero,
                    'complemento' => $request->complemento,
                    'bairro'      => $request->bairro,
                    'cidade'      => $request->cidade,
                    'estado'      => $request->estado,
                ]);
                $pessoa->update(['endereco_id' => $endereco->id]);
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
     * Exibe a pessoa especificada.
     *
     * @param  \App\Models\Pessoa  $pessoa
     * @return void
     */
    public function show(Pessoa $pessoa)
    {
        //
    }

    /**
     * Exibe o formulário para editar a pessoa especificada com todos os dados de papéis relacionados.
     *
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Pessoa $pessoa)
    {
        $function = __FUNCTION__;

        $pessoa->load(['cliente', 'fornecedor', 'funcionario', 'contatos', 'endereco']);

        $fieldGroups = [
            ['key' => 'geral', 'label' => 'Geral', 'fields' => $pessoa->generateFields($function)],
            ['key' => 'cliente', 'label' => 'Cliente', 'fields' => ($pessoa->cliente ?? new Cliente())->generateFields($function)],
            ['key' => 'fornecedor', 'label' => 'Fornecedor', 'fields' => ($pessoa->fornecedor ?? new Fornecedor())->generateFields($function)],
            ['key' => 'funcionario', 'label' => 'Funcionário', 'fields' => ($pessoa->funcionario ?? new Funcionario())->generateFields($function)],
            ['key' => 'contato', 'label' => 'Contato', 'fields' => ($pessoa->contatos->count() > 0
                ? $pessoa->contatos->first()
                : new Contato()
            )->generateFields($function)],
            ['key' => 'endereco', 'label' => 'Endereço', 'fields' => ($pessoa->endereco ?? new Endereco())->generateFields($function)],
        ];

        return view('pessoa.formulario', [
            'title' => 'Editar Pessoa',
            'route' => ['pessoa.atualizar', $pessoa->id],
            'fieldGroups' => $fieldGroups,
            'btn_label' => 'Salvar',
        ]);
    }

    /**
     * Atualiza a pessoa especificada e cria ou atualiza registros relacionados de contato, endereço e papéis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Pessoa $pessoa)
    {

        DB::beginTransaction();

        try {

            $pessoa->update([
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
                $contatoExistente = $pessoa->contatos->first();
                if ($contatoExistente) {
                    $contatoExistente->update([
                        'tipo_contato_id' => $request->tipo_contato,
                        'contato'         => $request->contato,
                    ]);
                } else {
                    Contato::create([
                        'pessoa_id'       => $pessoa->id,
                        'tipo_contato_id' => $request->tipo_contato,
                        'contato'         => $request->contato,
                    ]);
                }
            }

            if (! empty($request->cep)) {
                if ($pessoa->endereco) {
                    $pessoa->endereco->update([
                        'cep'         => $request->cep,
                        'logradouro'  => $request->logradouro,
                        'numero'      => $request->numero,
                        'complemento' => $request->complemento,
                        'bairro'      => $request->bairro,
                        'cidade'      => $request->cidade,
                        'estado'      => $request->estado,
                    ]);
                } else {
                    $endereco = Endereco::create([
                        'cep'         => $request->cep,
                        'logradouro'  => $request->logradouro,
                        'numero'      => $request->numero,
                        'complemento' => $request->complemento,
                        'bairro'      => $request->bairro,
                        'cidade'      => $request->cidade,
                        'estado'      => $request->estado,
                    ]);
                    $pessoa->update(['endereco_id' => $endereco->id]);
                }
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
     * Alterna o status ativo da pessoa especificada.
     *
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Http\RedirectResponse
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
     * Remove a pessoa especificada e todos os papéis, contatos e endereço associados.
     *
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Http\RedirectResponse
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
            if ($pessoa->contatos->count() > 0) {
                $pessoa->contatos->each->delete();
            }
            if ($pessoa->endereco) {
                $endereco = $pessoa->endereco;
                $pessoa->update(['endereco_id' => null]);
                $endereco->delete();
            }
            $pessoa->delete();
            DB::commit();
            return redirect()->route('pessoa.index')->with('success', 'Pessoa excluída com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pessoa.index')->withErrors(['db_error' => 'Erro ao excluir pessoa: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtém a configuração de abas para o formulário de pessoa.
     *
     * @return array<int, array{key: string, label: string, icon: string}>
     */
    private function getFormTabs(): array
    {
        return [
            ['key' => 'geral', 'label' => 'Geral', 'icon' => 'fa-solid fa-users'],
            ['key' => 'cliente', 'label' => 'Cliente', 'icon' => 'fa-solid fa-handshake'],
            ['key' => 'fornecedor', 'label' => 'Fornecedor', 'icon' => 'fa-solid fa-truck'],
            ['key' => 'funcionario', 'label' => 'Funcionário', 'icon' => 'fa-solid fa-id-badge'],
            ['key' => 'contato', 'label' => 'Contato', 'icon' => 'fa-solid fa-address-book'],
            ['key' => 'endereco', 'label' => 'Endereço', 'icon' => 'fa-solid fa-location-dot'],
        ];
    }

    /**
     * Gera um array de valores dos campos da requisição para dados flash da sessão.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
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
