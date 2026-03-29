<?php

namespace App\Http\Controllers;

use App\Models\Saida;
use Illuminate\Http\Request;

/**
 * Controlador responsável pelo gerenciamento de saídas.
 *
 * @package App\Http\Controllers
 */
class SaidaController extends Controller
{
    /**
     * Exibe a listagem do recurso.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Exibe o formulário para criação de um novo recurso.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Armazena um recurso recém-criado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param  \App\Models\Saida  $saida
     * @return void
     */
    public function show(Saida $saida)
    {
        //
    }

    /**
     * Exibe o formulário para edição do recurso especificado.
     *
     * @param  \App\Models\Saida  $saida
     * @return void
     */
    public function edit(Saida $saida)
    {
        //
    }

    /**
     * Atualiza o recurso especificado no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Saida  $saida
     * @return void
     */
    public function update(Request $request, Saida $saida)
    {
        //
    }

    /**
     * Remove o recurso especificado do banco de dados.
     *
     * @param  \App\Models\Saida  $saida
     * @return void
     */
    public function destroy(Saida $saida)
    {
        //
    }
}
