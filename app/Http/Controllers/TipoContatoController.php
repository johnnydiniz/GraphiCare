<?php
namespace App\Http\Controllers;

use App\Models\TipoContato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TipoContatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipoContatos = TipoContato::all();
        
        return view('tipo-contato.index', compact('tipoContatos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new TipoContato())->generateFields(__FUNCTION__);
        return view('tipo-contato.formulario', ['title' => 'Cadastrar Tipo Matéria-prima', 'route' => 'tipo-tipo-contato.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(TipoContato $tipoContato)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoContato $tipoContato)
    {
        $fields = $tipoContato->generateFields(__FUNCTION__);
        return view('tipo-contato.formulario', ['title' => 'Editar Matéria-prima', 'route' => ['tipo-contato.editar', $tipoContato->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'descricao' => $request->descricao,
        ];
        return $fields;
    }
}
