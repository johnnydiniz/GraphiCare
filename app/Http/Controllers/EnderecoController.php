<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;

/**
 * Gerencia operações CRUD relacionadas a endereços.
 *
 * @package App\Http\Controllers
 */
class EnderecoController extends Controller
{
    /**
     * Exibe a listagem de endereços.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Exibe o formulário para criação de um novo endereço.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Armazena um novo endereço no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Exibe os detalhes de um endereço específico.
     *
     * @param  \App\Models\Endereco  $endereco
     * @return void
     */
    public function show(Endereco $endereco)
    {
        //
    }

    /**
     * Exibe o formulário de edição de um endereço existente.
     *
     * @param  \App\Models\Endereco  $endereco
     * @return void
     */
    public function edit(Endereco $endereco)
    {
        //
    }

    /**
     * Atualiza um endereço existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Endereco  $endereco
     * @return void
     */
    public function update(Request $request, Endereco $endereco)
    {
        //
    }

    /**
     * Remove um endereço do banco de dados.
     *
     * @param  \App\Models\Endereco  $endereco
     * @return void
     */
    public function destroy(Endereco $endereco)
    {
        //
    }
}
