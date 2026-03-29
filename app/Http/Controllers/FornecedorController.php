<?php
namespace App\Http\Controllers;

use App\Models\Fornecedor;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Gerencia operações CRUD e ações relacionadas a fornecedores.
 *
 * @package App\Http\Controllers
 */
class FornecedorController extends Controller
{
    /**
     * Exibe a listagem de fornecedores.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $fornecedores = Fornecedor::with('pessoa')->get();

        return view('fornecedor.index', compact('fornecedores'));
    }

    /**
     * Exibe o formulário para criação de um novo fornecedor.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $fields = (new Fornecedor())->generateFields(__FUNCTION__);
        return view('fornecedor.formulario', ['title' => 'Cadastrar Fornecedor', 'route' => 'fornecedor.salvar', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Armazena um novo fornecedor no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
                'ativo'     => true,
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
     * Exibe os detalhes de um fornecedor específico.
     *
     * @param  \App\Models\Fornecedor  $fornecedor
     * @return void
     */
    public function show(Fornecedor $fornecedor)
    {
        //
    }

    /**
     * Exibe o formulário de edição de um fornecedor existente.
     *
     * @param  \App\Models\Fornecedor  $fornecedor
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Fornecedor $fornecedor)
    {
        $fields = $fornecedor->generateFields(__FUNCTION__);
        return view('fornecedor.formulario', ['title' => 'Editar Fornecedor', 'route' => ['fornecedor.atualizar', $fornecedor->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Atualiza um fornecedor existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fornecedor  $fornecedor
     * @return \Illuminate\Http\RedirectResponse
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
     * Alterna o status ativo/inativo de um fornecedor.
     *
     * @param  \App\Models\Fornecedor  $fornecedor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Fornecedor $fornecedor)
    {
        try {
            $fornecedor->update(['ativo' => !$fornecedor->ativo]);
            $status = $fornecedor->ativo ? __('activated') : __('deactivated');
            return redirect()->back()->with('success', __('Provider :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove um fornecedor do banco de dados.
     *
     * @param  \App\Models\Fornecedor  $fornecedor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Fornecedor $fornecedor)
    {
        DB::beginTransaction();
        try {
            $fornecedor->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Fornecedor excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['db_error' => 'Erro ao excluir fornecedor: ' . $e->getMessage()]);
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
