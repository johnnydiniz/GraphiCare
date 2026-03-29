<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use Illuminate\Http\Request;

/**
 * Gerencia operações CRUD relacionadas a entradas de estoque.
 *
 * @package App\Http\Controllers
 */
class EntradaController extends Controller
{
    /**
     * Exibe a listagem de entradas.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Exibe o formulário para criação de uma nova entrada.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Armazena uma nova entrada no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Exibe os detalhes de uma entrada específica.
     *
     * @param  \App\Models\Entrada  $entrada
     * @return void
     */
    public function show(Entrada $entrada)
    {
        //
    }

    /**
     * Exibe o formulário de edição de uma entrada existente.
     *
     * @param  \App\Models\Entrada  $entrada
     * @return void
     */
    public function edit(Entrada $entrada)
    {
        //
    }

    /**
     * Atualiza uma entrada existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Entrada  $entrada
     * @return void
     */
    public function update(Request $request, Entrada $entrada)
    {
        //
    }

    /**
     * Remove uma entrada do banco de dados.
     *
     * @param  \App\Models\Entrada  $entrada
     * @return void
     */
    public function destroy(Entrada $entrada)
    {
        //
    }
}
