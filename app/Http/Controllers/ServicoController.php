<?php
namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use NumberFormatter;

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
        $servicos = Servico::with('componentesServico')->get();

        return view('servico.index', compact('servicos', 'formatter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new Servico())->generateFields(__FUNCTION__);
        return view('servico.formulario', ['title' => 'Cadastrar Serviço', 'route' => 'servico.inserir', 'fields' => $fields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao'        => 'required|string|unique:servicos',
            'qtde'             => 'required|numeric',
            'valor'            => 'required|numeric',
            'custo'            => 'required|numeric',
            'tempo_realizacao' => 'required|date_format:H:i',
        ]);
        if ($validator->fails()) {
            $fields = $this->generateSessionFields($request);
            return back()->with($fields)->withErrors($validator);
        }

        DB::beginTransaction();

        try {

            Servico::create([
                'ativo'            => true,
                'descricao'        => $request->descricao,
                'qtde'             => $request->qtde ?? 0,
                'valor'            => $request->valor ?? 0,
                'custo'            => $request->custo ?? 0,
                'tempo_realizacao' => $request->tempo_realizacao,
            ]);

            DB::commit();
            return redirect()->route('servico.index')->with('success', 'Serviço cadastrado com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar serviço: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Servico $servico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servico $servico)
    {
        $fields = $servico->generateFields(__FUNCTION__);
        return view('servico.formulario', ['title' => 'Editar Serviço', 'route' => ['servico.editar', $servico->id], 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servico $servico)
    {
        DB::beginTransaction();

        try {

            $servico->update([
                'descricao'        => $request->descricao,
                'qtde'             => $request->qtde,
                'valor'            => $request->valor,
                'custo'            => $request->custo,
                'tempo_realizacao' => $request->tempo_realizacao,
            ]);

            DB::commit();
            return redirect()->route('servico.index')->with('success', 'Alterações efetuadas com sucesso.');
        } catch (\Exception $e) {
            $fields = $this->generateSessionFields($request);
            DB::rollBack();
            return back()->with($fields)->withErrors(['db_error' => 'Erro ao cadastrar serviço: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servico $servico)
    {
        DB::beginTransaction();
        try {
            $servico->delete();
            DB::commit();
            return redirect()->route('servico.index')->with('success', 'Serviço excluído com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('servico.index')->withErrors(['db_error' => 'Erro ao excluir serviço: ' . $e->getMessage()]);
        }
    }

    public function generateSessionFields(Request $request)
    {
        $fields = [
            'descricao'        => $request->descricao,
            'qtde'             => $request->qtde,
            'valor'            => $request->valor,
            'custo'            => $request->custo,
            'tempo_realizacao' => $request->tempo_realizacao,
        ];
        return $fields;
    }
}
