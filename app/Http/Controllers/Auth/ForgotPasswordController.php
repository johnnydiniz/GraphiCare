<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

/**
 * Controlador responsável pelo envio de e-mails de redefinição de senha.
 *
 * @package App\Http\Controllers\Auth
 */
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Controlador de Redefinição de Senha
    |--------------------------------------------------------------------------
    |
    | Este controlador é responsável por lidar com e-mails de redefinição
    | de senha e inclui uma trait que auxilia no envio dessas notificações
    | da sua aplicação para os seus usuários. Fique à vontade para
    | explorar essa trait.
    |
    */

    use SendsPasswordResetEmails;
}
