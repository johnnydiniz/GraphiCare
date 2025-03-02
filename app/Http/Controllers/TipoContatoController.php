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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoContato $tipoContato)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoContato $tipoContato)
    {
        //
    }
}
