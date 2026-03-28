<?php
namespace App\Http\Controllers;

use App\Models\TipoMateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TipoMateriaPrimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipoMateriaPrimas = TipoMateriaPrima::all();
        $title = 'Raw Material Types';
        $route = 'tipo-materia-prima.salvar';

        return view('tipo-materia-prima.index', compact('tipoMateriaPrimas', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new TipoMateriaPrima())->generateFields(__FUNCTION__);
        return view('tipo-materia-prima.formulario', ['title' => 'Cadastrar Tipo Matéria-prima', 'route' => 'tipo-materia-prima.salvar', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(TipoMateriaPrima $tipoMateriaPrima)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoMateriaPrima $tipoMateriaPrima)
    {
        $fields = $tipoMateriaPrima->generateFields(__FUNCTION__);
        return view('tipo-materia-prima.formulario', ['title' => 'Editar Matéria-prima', 'route' => ['tipo-materia-prima.atualizar', $tipoMateriaPrima->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
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
     * Toggle the active status of the specified resource.
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
     * Remove the specified resource from storage.
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

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'descricao' => $request->descricao,
        ];
        return $fields;
    }
}
