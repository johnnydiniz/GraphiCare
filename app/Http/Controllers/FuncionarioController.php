<?php
namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Gerencia operações CRUD e ações relacionadas a funcionários.
 *
 * @package App\Http\Controllers
 */
class FuncionarioController extends Controller
{
    /**
     * Exibe a listagem de funcionários.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $funcionarios = Funcionario::with('pessoa')->get();

        return view('funcionario.index', compact('funcionarios'));
    }

    /**
     * Exibe o formulário para criação de um novo funcionário.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $fields = (new Funcionario())->generateFields(__FUNCTION__);
        return view('funcionario.formulario', ['title' => 'Cadastrar Funcionário', 'route' => 'funcionario.salvar', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Armazena um novo funcionário no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'         => 'required|string|max:255|unique:pessoas',
            'cpf_cnpj'      => 'required|string|max:14|unique:pessoas',
            'nome_registro' => 'required|string|max:255',
            'senha'         => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

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

            Funcionario::create([
                'ativo'     => true,
                'pessoa_id' => $pessoa->id,
                'cargo'     => $request->cargo,
                'salario'   => $request->salario,
            ]);

            DB::commit();
            return redirect()->route('funcionario.index')->with('success', 'Funcionário cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Exibe os detalhes de um funcionário específico.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return void
     */
    public function show(Funcionario $funcionario)
    {
        //
    }

    /**
     * Exibe o formulário de edição de um funcionário existente.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Funcionario $funcionario)
    {
        $fields = $funcionario->generateFields(__FUNCTION__);
        return view('funcionario.formulario', ['title' => 'Editar Funcionário', 'route' => ['funcionario.atualizar', $funcionario->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Atualiza um funcionário existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Funcionario  $funcionario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Funcionario $funcionario)
    {
        DB::beginTransaction();
        $funcionario = $funcionario->load('pessoa');

        try {

            $funcionario->pessoa->update([
                'nome_registro' => $request->nome_registro,
                'nome_social'   => $request->nome_social ?? $request->nome_registro,
                'login'         => $request->login,
                'senha'         => $request->senha ? bcrypt($request->senha) : $funcionario->pessoa->senha,
                'cpf_cnpj'      => $request->cpf_cnpj,
            ]);

            $funcionario->update([
                'cargo' => $request->cargo,
            ]);

            DB::commit();
            return redirect()->route('funcionario.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Alterna o status ativo/inativo de um funcionário.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Funcionario $funcionario)
    {
        try {
            $funcionario->update(['ativo' => !$funcionario->ativo]);
            $status = $funcionario->ativo ? __('activated') : __('deactivated');
            return redirect()->back()->with('success', __('Employee :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove um funcionário do banco de dados.
     *
     * @param  \App\Models\Funcionario  $funcionario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Funcionario $funcionario)
    {
        DB::beginTransaction();
        try {
            $funcionario->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Funcionário excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['db_error' => 'Erro ao excluir funcionário: ' . $e->getMessage()]);
        }
    }

    /**
     * Gera os campos de sessão a partir da requisição para reexibição do formulário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
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
