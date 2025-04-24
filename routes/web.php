<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/autenticar', 'App\Http\Controllers\Auth\LoginController@login')->name('autenticar');
Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('sair');

Route::middleware(['auth'])->group(function () {

    //Pessoa
    Route::get('/pessoa', 'App\Http\Controllers\PessoaController@index')->name('pessoa.index');
    Route::get('/pessoa/visualizar', 'App\Http\Controllers\PessoaController@show')->name('pessoa.visualizar');
    Route::get('/pessoa/inserir', 'App\Http\Controllers\PessoaController@create')->name('pessoa.inserir');
    Route::post('/pessoa/inserir', 'App\Http\Controllers\PessoaController@store')->name('pessoa.inserir');
    Route::get('/pessoa/editar/{pessoa}', 'App\Http\Controllers\PessoaController@edit')->name('pessoa.editar');
    Route::put('/pessoa/editar/{pessoa}', 'App\Http\Controllers\PessoaController@update')->name('pessoa.editar');
    Route::delete('/pessoa/excluir/{pessoa}', 'App\Http\Controllers\PessoaController@destroy')->name('pessoa.excluir');

    //Tipos de contatos
    Route::get('/tipo-contato', 'App\Http\Controllers\TipoContatoController@index')->name('tipo-contato.index');
    Route::get('/tipo-contato/visualizar', 'App\Http\Controllers\TipoContatoController@show')->name('tipo-contato.visualizar');
    Route::get('/tipo-contato/inserir', 'App\Http\Controllers\TipoContatoController@create')->name('tipo-contato.inserir');
    Route::post('/tipo-contato/inserir', 'App\Http\Controllers\TipoContatoController@store')->name('tipo-contato.inserir');
    Route::get('/tipo-contato/editar/{tipoContato}', 'App\Http\Controllers\TipoContatoController@edit')->name('tipo-contato.editar');
    Route::put('/tipo-contato/editar/{tipoContato}', 'App\Http\Controllers\TipoContatoController@update')->name('tipo-contato.editar');
    Route::delete('/tipo-contato/excluir/{tipoContato}', 'App\Http\Controllers\TipoContatoController@destroy')->name('tipo-contato.excluir');
        
    //Funcionário
    Route::get('/funcionario', 'App\Http\Controllers\FuncionarioController@index')->name('funcionario.index');
    Route::get('/funcionario/visualizar', 'App\Http\Controllers\FuncionarioController@show')->name('funcionario.visualizar');
    Route::get('/funcionario/inserir', 'App\Http\Controllers\FuncionarioController@create')->name('funcionario.inserir');
    Route::post('/funcionario/inserir', 'App\Http\Controllers\FuncionarioController@store')->name('funcionario.inserir');
    Route::get('/funcionario/editar/{funcionario}', 'App\Http\Controllers\FuncionarioController@edit')->name('funcionario.editar');
    Route::put('/funcionario/editar/{funcionario}', 'App\Http\Controllers\FuncionarioController@update')->name('funcionario.editar');
    Route::delete('/funcionario/excluir/{funcionario}', 'App\Http\Controllers\FuncionarioController@destroy')->name('funcionario.excluir');

    //Cliente
    Route::get('/cliente', 'App\Http\Controllers\ClienteController@index')->name('cliente.index');
    Route::get('/cliente/visualizar', 'App\Http\Controllers\ClienteController@show')->name('cliente.visualizar');
    Route::get('/cliente/inserir', 'App\Http\Controllers\ClienteController@create')->name('cliente.inserir');
    Route::post('/cliente/inserir', 'App\Http\Controllers\ClienteController@store')->name('cliente.inserir');
    Route::get('/cliente/editar/{cliente}', 'App\Http\Controllers\ClienteController@edit')->name('cliente.editar');
    Route::put('/cliente/editar/{cliente}', 'App\Http\Controllers\ClienteController@update')->name('cliente.editar');
    Route::delete('/cliente/excluir/{cliente}', 'App\Http\Controllers\ClienteController@destroy')->name('cliente.excluir');

    //Fornecedores
    Route::get('/fornecedor', 'App\Http\Controllers\FornecedorController@index')->name('fornecedor.index');
    Route::get('/fornecedor/visualizar', 'App\Http\Controllers\FornecedorController@show')->name('fornecedor.visualizar');
    Route::get('/fornecedor/inserir', 'App\Http\Controllers\FornecedorController@create')->name('fornecedor.inserir');
    Route::post('/fornecedor/inserir', 'App\Http\Controllers\FornecedorController@store')->name('fornecedor.inserir');
    Route::get('/fornecedor/editar/{fornecedor}', 'App\Http\Controllers\FornecedorController@edit')->name('fornecedor.editar');
    Route::put('/fornecedor/editar/{fornecedor}', 'App\Http\Controllers\FornecedorController@update')->name('fornecedor.editar');
    Route::delete('/fornecedor/excluir/{fornecedor}', 'App\Http\Controllers\FornecedorController@destroy')->name('fornecedor.excluir');

    //Matérias-primas
    Route::get('/materia-prima', 'App\Http\Controllers\MateriaPrimaController@index')->name('materia-prima.index');
    Route::get('/materia-prima/visualizar', 'App\Http\Controllers\MateriaPrimaController@show')->name('materia-prima.visualizar');
    Route::get('/materia-prima/inserir', 'App\Http\Controllers\MateriaPrimaController@create')->name('materia-prima.inserir');
    Route::post('/materia-prima/inserir', 'App\Http\Controllers\MateriaPrimaController@store')->name('materia-prima.inserir');
    Route::get('/materia-prima/editar/{materiaPrima}', 'App\Http\Controllers\MateriaPrimaController@edit')->name('materia-prima.editar');
    Route::put('/materia-prima/editar/{materiaPrima}', 'App\Http\Controllers\MateriaPrimaController@update')->name('materia-prima.editar');
    Route::delete('/materia-prima/excluir/{materiaPrima}', 'App\Http\Controllers\MateriaPrimaController@destroy')->name('materia-prima.excluir');

    //Tipos de matérias-primas
    Route::get('/tipo-materia-prima', 'App\Http\Controllers\TipoMateriaPrimaController@index')->name('tipo-materia-prima.index');
    Route::get('/tipo-materia-prima/visualizar', 'App\Http\Controllers\TipoMateriaPrimaController@show')->name('tipo-materia-prima.visualizar');
    Route::get('/tipo-materia-prima/inserir', 'App\Http\Controllers\TipoMateriaPrimaController@create')->name('tipo-materia-prima.inserir');
    Route::post('/tipo-materia-prima/inserir', 'App\Http\Controllers\TipoMateriaPrimaController@store')->name('tipo-materia-prima.inserir');
    Route::get('/tipo-materia-prima/editar/{tipoMateriaPrima}', 'App\Http\Controllers\TipoMateriaPrimaController@edit')->name('tipo-materia-prima.editar');
    Route::put('/tipo-materia-prima/editar/{tipoMateriaPrima}', 'App\Http\Controllers\TipoMateriaPrimaController@update')->name('tipo-materia-prima.editar');
    Route::delete('/tipo-materia-prima/excluir/{tipoMateriaPrima}', 'App\Http\Controllers\TipoMateriaPrimaController@destroy')->name('tipo-materia-prima.excluir');
    
    //Serviços
    Route::get('/servico', 'App\Http\Controllers\ServicoController@index')->name('servico.index');
    Route::get('/servico/visualizar', 'App\Http\Controllers\ServicoController@show')->name('servico.visualizar');
    Route::get('/servico/inserir', 'App\Http\Controllers\ServicoController@create')->name('servico.inserir');
    Route::post('/servico/inserir', 'App\Http\Controllers\ServicoController@store')->name('servico.inserir');
    Route::get('/servico/editar/{servico}', 'App\Http\Controllers\ServicoController@edit')->name('servico.editar');
    Route::put('/servico/editar/{servico}', 'App\Http\Controllers\ServicoController@update')->name('servico.editar');
    Route::delete('/servico/excluir/{servico}', 'App\Http\Controllers\ServicoController@destroy')->name('servico.excluir');

    //Componentes de serviços
    Route::get('/componente-servico', 'App\Http\Controllers\ComponenteServicoController@index')->name('componente-servico.index');
    Route::get('/componente-servico/visualizar', 'App\Http\Controllers\ComponenteServicoController@show')->name('componente-servico.visualizar');
    Route::get('/componente-servico/inserir', 'App\Http\Controllers\ComponenteServicoController@create')->name('componente-servico.inserir');
    Route::post('/componente-servico/inserir', 'App\Http\Controllers\ComponenteServicoController@store')->name('componente-servico.inserir');
    Route::get('/componente-servico/editar/{componenteServico}', 'App\Http\Controllers\ComponenteServicoController@edit')->name('componente-servico.editar');
    Route::put('/componente-servico/editar/{componenteServico}', 'App\Http\Controllers\ComponenteServicoController@update')->name('componente-servico.editar');
    Route::delete('/componente-servico/excluir/{componenteServico}', 'App\Http\Controllers\ComponenteServicoController@destroy')->name('componente-servico.excluir');

    //Orçamentos
    Route::get('/orcamento', 'App\Http\Controllers\OrcamentoController@index')->name('orcamento.index');
    Route::get('/orcamento/visualizar', 'App\Http\Controllers\OrcamentoController@show')->name('orcamento.visualizar');
    Route::get('/orcamento/inserir', 'App\Http\Controllers\OrcamentoController@create')->name('orcamento.inserir');
    Route::post('/orcamento/inserir', 'App\Http\Controllers\OrcamentoController@store')->name('orcamento.inserir');
    Route::get('/orcamento/editar/{orcamento}', 'App\Http\Controllers\OrcamentoController@edit')->name('orcamento.editar');
    Route::put('/orcamento/editar/{orcamento}', 'App\Http\Controllers\OrcamentoController@update')->name('orcamento.editar');
    Route::delete('/orcamento/excluir/{orcamento}', 'App\Http\Controllers\OrcamentoController@destroy')->name('orcamento.excluir');

});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//É necessário fazer as telas para tipos de contato, visto que o usuário precisa ter a possibilidade de editar ou excluir um tipo de contato
//Colocar alguma informação em um orçamento, na listagem geral, se ele gerou ordens de serviço ou não
//Adicionar um campo de observações em orçamentos