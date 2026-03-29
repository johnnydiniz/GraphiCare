<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Controller responsável pelo gerenciamento do perfil do usuário autenticado.
 *
 * @package App\Http\Controllers
 */
class PerfilController extends Controller
{
    /**
     * Cria uma nova instância do controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a página de perfil do usuário autenticado.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $pessoa = Auth::user();
        return view('perfil.index', compact('pessoa'));
    }

    /**
     * Atualiza as informações de perfil do usuário autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $pessoa = Auth::user();

        $request->validate([
            'nome_registro' => 'required|string|max:255',
            'nome_social' => 'nullable|string|max:255',
            'login' => ['required', 'string', 'max:255', Rule::unique('pessoas', 'login')->ignore($pessoa->id)],
        ]);

        $pessoa->nome_registro = $request->nome_registro;
        $pessoa->nome_social = $request->nome_social;
        $pessoa->login = $request->login;
        $pessoa->save();

        return redirect()->route('perfil.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    /**
     * Atualiza a senha do usuário autenticado após verificar a senha atual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required',
            'senha' => 'required|string|min:6|confirmed',
        ]);

        $pessoa = Auth::user();

        if (!Hash::check($request->senha_atual, $pessoa->senha)) {
            return back()->withErrors(['senha_atual' => 'A senha atual está incorreta.']);
        }

        $pessoa->senha = $request->senha;
        $pessoa->save();

        return redirect()->route('perfil.index')->with('success', 'Senha alterada com sucesso.');
    }
}
