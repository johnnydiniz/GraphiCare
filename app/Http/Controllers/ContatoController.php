<?php

namespace App\Http\Controllers;

use App\Models\Contato;
use Illuminate\Http\Request;

/**
 * Gerencia operações CRUD relacionadas a contatos.
 *
 * @package App\Http\Controllers
 */
class ContatoController extends Controller
{
    /**
     * Exibe a listagem de contatos.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Exibe o formulário para criação de um novo contato.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Armazena um novo contato no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Exibe os detalhes de um contato específico.
     *
     * @param  \App\Models\Contato  $contato
     * @return void
     */
    public function show(Contato $contato)
    {
        //
    }

    /**
     * Exibe o formulário de edição de um contato existente.
     *
     * @param  \App\Models\Contato  $contato
     * @return void
     */
    public function edit(Contato $contato)
    {
        //
    }

    /**
     * Atualiza um contato existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contato  $contato
     * @return void
     */
    public function update(Request $request, Contato $contato)
    {
        //
    }

    /**
     * Remove um contato do banco de dados.
     *
     * @param  \App\Models\Contato  $contato
     * @return void
     */
    public function destroy(Contato $contato)
    {
        //
    }
}
