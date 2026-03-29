<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

/**
 * Controlador responsável pela confirmação de senha do usuário.
 *
 * @package App\Http\Controllers\Auth
 */
class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Controlador de Confirmação de Senha
    |--------------------------------------------------------------------------
    |
    | Este controlador é responsável por lidar com confirmações de senha e
    | utiliza uma trait simples para incluir o comportamento. Você é livre
    | para explorar essa trait e sobrescrever quaisquer funções que
    | necessitem de personalização.
    |
    */

    use ConfirmsPasswords;

    /**
     * Para onde redirecionar os usuários quando a URL pretendida falha.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Cria uma nova instância do controlador.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
