<?php
namespace App\Http\Controllers;

use App\Models\TipoContato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador responsável pelo gerenciamento de tipos de contato.
 *
 * @package App\Http\Controllers
 */
class TipoContatoController extends Controller
{
    /**
     * Exibe a listagem do recurso.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $tipoContatos = TipoContato::all();
        $title = 'Contact Types';
        $route = 'tipo-contato.salvar';

        return view('tipo-contato.index', compact('tipoContatos', 'title', 'route'));
    }

    /**
     * Exibe o formulário para criação de um novo recurso.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $fields = (new TipoContato())->generateFields(__FUNCTION__);
        return view('tipo-contato.formulario', ['title' => 'Cadastrar Tipo Matéria-prima', 'route' => 'tipo-contato.salvar', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Armazena um recurso recém-criado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|unique:tipo_contatos',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->get('descricao'),
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $tipoContato = TipoContato::create([
                'ativo'                 => true,
                'descricao'             => $request->descricao,
            ]);

            DB::commit();
            return response()->json(['success' => 'Tipo de contato cadastrado com sucesso.', 'id' => $tipoContato->id], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $validator->errors()->get('descricao'),
                'errors' => $validator->errors()
            ], 422);
        }
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param  \App\Models\TipoContato  $tipoContato
     * @return void
     */
    public function show(TipoContato $tipoContato)
    {
        //
    }

    /**
     * Exibe o formulário para edição do recurso especificado.
     *
     * @param  \App\Models\TipoContato  $tipoContato
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(TipoContato $tipoContato)
    {
        $fields = $tipoContato->generateFields(__FUNCTION__);
        return view('tipo-contato.formulario', ['title' => 'Editar Matéria-prima', 'route' => ['tipo-contato.atualizar', $tipoContato->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Atualiza o recurso especificado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoContato  $tipoContato
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TipoContato $tipoContato)
    {
        DB::beginTransaction();

        try {

            $tipoContato->update([
                'descricao' => $request->descricao,
            ]);

            DB::commit();
            return redirect()->route('tipo-contato.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar tipo de contato: ' . $e->getMessage()]);
        }
    }

    /**
     * Alterna o status ativo do recurso especificado.
     *
     * @param  \App\Models\TipoContato  $tipoContato
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(TipoContato $tipoContato)
    {
        try {
            $tipoContato->update(['ativo' => !$tipoContato->ativo]);
            $status = $tipoContato->ativo ? __('activated') : __('deactivated');
            return redirect()->route('tipo-contato.index')->with('success', __('Contact type :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('tipo-contato.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove o recurso especificado do banco de dados.
     *
     * @param  \App\Models\TipoContato  $tipoContato
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TipoContato $tipoContato)
    {
        DB::beginTransaction();
        try {
            $tipoContato->delete();
            DB::commit();
            return redirect()->route('tipo-contato.index')->with('success', 'Matéria-prima excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tipo-contato.index')->withErrors(['db_error' => 'Erro ao excluir tipo de contato: ' . $e->getMessage()]);
        }
    }

    /**
     * Gera os campos da sessão a partir da requisição.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function generateSessionFields(Request $request)
    {
        $fields = [
            'descricao' => $request->descricao,
        ];
        return $fields;
    }
}
