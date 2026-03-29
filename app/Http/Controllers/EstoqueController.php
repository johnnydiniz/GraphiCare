<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use Illuminate\Http\Request;

/**
 * Gerencia operações CRUD relacionadas ao estoque.
 *
 * @package App\Http\Controllers
 */
class EstoqueController extends Controller
{
    /**
     * Exibe a listagem de itens do estoque.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Exibe o formulário para criação de um novo item de estoque.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Armazena um novo item de estoque no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Exibe os detalhes de um item de estoque específico.
     *
     * @param  \App\Models\Estoque  $estoque
     * @return void
     */
    public function show(Estoque $estoque)
    {
        //
    }

    /**
     * Exibe o formulário de edição de um item de estoque existente.
     *
     * @param  \App\Models\Estoque  $estoque
     * @return void
     */
    public function edit(Estoque $estoque)
    {
        //
    }

    /**
     * Atualiza um item de estoque existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Estoque  $estoque
     * @return void
     */
    public function update(Request $request, Estoque $estoque)
    {
        //
    }

    /**
     * Remove um item de estoque do banco de dados.
     *
     * @param  \App\Models\Estoque  $estoque
     * @return void
     */
    public function destroy(Estoque $estoque)
    {
        //
    }
}
