<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        return Auth::check() ? redirect()->route('biblioteca') : view('welcome');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'cpf' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials['cpf'] = preg_replace('/\D+/', '', $credentials['cpf']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['cpf' => 'CPF ou senha inválidos.'])->onlyInput('cpf');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('biblioteca'));
    }

    public function showRegistration(): View|RedirectResponse
    {
        return Auth::check() ? redirect()->route('biblioteca') : view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge(['cpf' => preg_replace('/\D+/', '', (string) $request->input('cpf', ''))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'digits:11', 'unique:users,cpf'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Este e-mail já está cadastrado.',
            'cpf.digits' => 'Informe os 11 números do CPF.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ]);

        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('biblioteca');
    }

    public function library(): RedirectResponse
    {
        return redirect()->route('books.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}