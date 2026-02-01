<?php
namespace App\Http\Controllers;

use App\Models\ComponenteServico;
use App\Models\MateriaPrima;
use App\Models\TipoMateriaPrima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MateriaPrimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materiasPrimas = MateriaPrima::with('tipoMateriaPrima')->get();
        $title = 'Raw Materials';
        $route = 'materia-prima.inserir';

        return view('materia-prima.index', compact('materiasPrimas', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new MateriaPrima())->generateFields(__FUNCTION__);
        return view('materia-prima.formulario', ['title' => 'Cadastrar Matéria-prima', 'route' => 'materia-prima.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|unique:materias_primas',
            'custo_medio' => 'required|numeric',
            'estoque_atual' => 'required|numeric',
            'estoque_minimo' => 'nullable|numeric',
            'tipo' => 'required|exists:tipo_materias_primas,id',
        ]);
        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $materiaPrima = MateriaPrima::create([
                'ativo' => true,
                'descricao' => $request->descricao,
                'custo_medio' => $request->custo_medio ?? 0,
                'estoque_atual' => $request->estoque_atual ?? 0,
                'estoque_minimo' => $request->estoque_minimo ?? null,
                'aviso_estoque' => $request->aviso_estoque ? true : false,
                'tipo_materia_prima_id' => $request->tipo,
            ]);

            // Buscar o tipo de matéria-prima para montar a descrição
            $tipoMateriaPrima = TipoMateriaPrima::find($request->tipo);
            $descricaoComponente = $tipoMateriaPrima ? $tipoMateriaPrima->descricao . ' ' . $materiaPrima->descricao : $materiaPrima->descricao;

            // Auto-criar ComponenteServico
            ComponenteServico::create([
                'ativo' => true,
                'tipo' => 'material',
                'descricao' => $descricaoComponente,
                'materia_prima_id' => $materiaPrima->id,
                'equipamento_operacional_id' => null,
                'custo_operacional' => $materiaPrima->custo_medio,
            ]);

            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Matéria-prima cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar matéria-prima: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MateriaPrima $materiaPrima)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MateriaPrima $materiaPrima)
    {
        $fields = $materiaPrima->generateFields(__FUNCTION__);
        return view('materia-prima.formulario', ['title' => 'Editar Matéria-prima', 'route' => ['materia-prima.editar', $materiaPrima->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MateriaPrima $materiaPrima)
    {
        DB::beginTransaction();

        try {

            $materiaPrima->update([
                'descricao' => $request->descricao,
                'custo_medio' => $request->custo_medio,
                'estoque_atual' => $request->estoque_atual,
                'estoque_minimo' => $request->estoque_minimo,
                'aviso_estoque' => $request->aviso_estoque ? true : false,
                'tipo_materia_prima_id' => $request->tipo,
            ]);

            // Atualizar o ComponenteServico associado
            $tipoMateriaPrima = TipoMateriaPrima::find($request->tipo);
            $descricaoComponente = $tipoMateriaPrima ? $tipoMateriaPrima->descricao . ' ' . $request->descricao : $request->descricao;

            ComponenteServico::where('materia_prima_id', $materiaPrima->id)->update([
                'descricao' => $descricaoComponente,
                'custo_operacional' => $request->custo_medio,
            ]);

            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar matéria-prima: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(MateriaPrima $materiaPrima)
    {
        try {
            $materiaPrima->update(['ativo' => !$materiaPrima->ativo]);
            $status = $materiaPrima->ativo ? __('activated') : __('deactivated');
            return redirect()->route('materia-prima.index')->with('success', __('Raw material :status successfully.', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->route('materia-prima.index')->withErrors(['db_error' => __('Error changing status: ') . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MateriaPrima $materiaPrima)
    {
        DB::beginTransaction();
        try {
            $materiaPrima->delete();
            DB::commit();
            return redirect()->route('materia-prima.index')->with('success', 'Matéria-prima excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('materia-prima.index')->withErrors(['db_error' => 'Erro ao excluir matéria-prima: ' . $e->getMessage()]);
        }
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'tipo' => $request->tipo,
            'descricao' => $request->descricao,
            'custo_medio' => $request->custo_medio,
            'estoque_atual' => $request->estoque_atual,
            'estoque_minimo' => $request->estoque_minimo,
            'aviso_estoque' => $request->aviso_estoque,
        ];
        return $fields;
    }
}
