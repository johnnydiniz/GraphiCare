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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoMateriaPrima $tipoMateriaPrima)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoMateriaPrima $tipoMateriaPrima)
    {
        //
    }
}
