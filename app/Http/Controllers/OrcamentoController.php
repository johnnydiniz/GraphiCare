<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use Illuminate\Http\Request;
use NumberFormatter;
use Illuminate\Support\Str;

class OrcamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
        $orcamentos = Orcamento::all();
        $title = 'Quotes';
        $route = 'orcamento.inserir';
        return view('orcamento.index', compact('orcamentos', 'formatter', 'title', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = (new Orcamento())->generateFields(__FUNCTION__);
        $subfields = (new Orcamento())->generateFields(__FUNCTION__);
        return view('orcamento.formulario', ['title' => 'Cadastrar Orçamento', 'route' => 'orcamento.inserir', 'fields' => $fields, 'subfields' => $subfields, 'btn_label' => 'Cadastrar']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Orcamento $orcamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orcamento $orcamento)
    {
        $fields = $orcamento->generateFields(__FUNCTION__);
        return view('orcamento.formulario', ['title' => 'Editar Matéria-prima', 'route' => ['orcamento.editar', $orcamento->id] , 'fields' => $fields, 'btn_label' => 'Salvar']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orcamento $orcamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orcamento $orcamento)
    {
        //
    }
}
