<?php
namespace App\Http\Controllers;

use App\Models\TipoMateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador responsável pelo gerenciamento de tipos de matéria-prima.
 *
 * @package App\Http\Controllers
 */
class TipoMateriaPrimaController extends Controller
{
    /**
     * Exibe a listagem do recurso.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $tipoMateriaPrimas = TipoMateriaPrima::all();
        $title = 'Raw Material Types';
        $route = 'tipo-materia-prima.salvar';

        return view('tipo-materia-prima.index', compact('tipoMateriaPrimas', 'title', 'route'));
    }

    /**
     * Exibe o formulário para criação de um novo recurso.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $fields = (new TipoMateriaPrima())->generateFields(__FUNCTION__);
        return view('tipo-materia-prima.formulario', ['title' => 'Cadastrar Tipo Matéria-prima', 'route' => 'tipo-materia-prima.salvar', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
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
            'descricao' => 'required|string|unique:tipo_materias_primas',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->get('descricao'),
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $tipoMateriaPrima = TipoMateriaPrima::create([
                'ativo'                 => true,
                'descricao'             => $request->descricao,
            ]);

            DB::commit();
            return response()->json(['success' => 'Tipo de matéria prima cadastrado com sucesso.', 'id' => $tipoMateriaPrima->id], 200);
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
     * @param  \App\Models\TipoMateriaPrima  $tipoMateriaPrima
     * @return void
     */
    public function show(TipoMateriaPrima $tipoMateriaPrima)
    {
        //
    }

    /**
     * Exibe o formulário para edição do recurso especificado.
     *
     * @param  \App\Models\TipoMateriaPrima  $tipoMateriaPrima
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(TipoMateriaPrima $tipoMateriaPrima)
    {
        $fields = $tipoMateriaPrima->generateFields(__FUNCTION__);
        return view('tipo-materia-prima.formulario', ['title' => 'Editar Matéria-prima', 'route' => ['tipo-materia-prima.atualizar', $tipoMateriaPrima->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Atualiza o recurso especificado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoMateriaPrima  $tipoMateriaPrima
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TipoMateriaPrima $tipoMateriaPrima)
    {
        DB::beginTransaction();

        try {

            $tipoMateriaPrima->update([
                'descricao' => $request->descricao,
            ]);

            DB::commit();
            return redirect()->route('tipo-materia-prima.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar tipo de matéria-prima: ' . $e->getMessage()]);
        }
    }

    /**
     * Alterna o status ativo do recurso especificado.
     *
     * @param  \App\Models\TipoMateriaPrima  $tipoMateriaPrima
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(TipoMateriaPrima $tipoMateriaPrima)
    {
        try {
            $tipoMateriaPrima->update(['ativo' => !$tipoMateriaPrima->ativo]);
            $status = $tipoMateriaPrima->ativo ? __('activated') : __('deactivated');
            return redirect()->route('tipo-materia-prima.index')->with('success', __('Raw material type :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('tipo-materia-prima.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove o recurso especificado do banco de dados.
     *
     * @param  \App\Models\TipoMateriaPrima  $tipoMateriaPrima
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TipoMateriaPrima $tipoMateriaPrima)
    {
        DB::beginTransaction();
        try {
            $tipoMateriaPrima->delete();
            DB::commit();
            return redirect()->route('tipo-materia-prima.index')->with('success', 'Matéria-prima excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tipo-materia-prima.index')->withErrors(['db_error' => 'Erro ao excluir tipo de matéria-prima: ' . $e->getMessage()]);
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
