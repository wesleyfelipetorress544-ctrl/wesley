<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->is_admin
                ? redirect()->route('biblioteca')
                : redirect()->route('books.index');
        }

        return view('auth.admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'cpf' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        $credentials['cpf'] = preg_replace('/\\D+/', '', $credentials['cpf']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['cpf' => 'CPF ou senha invÃ¡lidos.'])->onlyInput('cpf');
        }

        if (! $request->user()->is_admin) {
            Auth::logout();

            return back()->withErrors(['cpf' => 'Esta conta nÃ£o tem acesso administrativo.'])->onlyInput('cpf');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('reports.index'));
    }

    Public function usuario_html(Request $request)
    {
        return view('welcome');
    }
    
    public function cadastrarUsuario(Request $request){

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuario',
            'senha' => 'required|string|min:8',
            'tipo' => 'required|in:Aluno,Funcionario',
        ]);

        try {

            $usuario = new Usuario();
            $usuario->nome = $request->nome;
            $usuario->email = $request->email;
            $usuario->senha = Hash::make($request->senha);
            $usuario->tipo = $request->tipo;
            $usuario->save();

            return response()->json(['message' => 'Usuário cadastrado com sucesso!','erro' =>'n'], 200);


        }catch (\Throwable $th) {
            
            return response()->json(['message' => 'Erro ao cadastrar usuário', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
        }

    }

    public function ver_usuario(Request $request, int $id){
        $usuario = Usuario::find($id);
        if ($usuario) {
            $usuario->load('login_funcionario');
        }
        if ($usuario) {
            return response()->json(['usuario' => $usuario, 'erro' => 'n'], 200);
            } else {
            return response()->json(['message' => 'Usuário não encontrado', 'erro' => 's'], 200);
            }
    }
    public function listar_usuarios(Request $request){
        $usuarios = Usuario::all();
        $usuarios->load('login_funcionario');
        return response()->json(['usuarios' => $usuarios, 'erro' => 'n'], 200);
    }
     public function listar_usuarios_simples(Request $request){
        $usuarios = Usuario::select('nome', 'tipo')->get();
        return response()->json(['usuarios' => $usuarios, 'erro' => 'n'], 200);
    }
    
    public function alterar_usuario(Request $request){

            $request->validate([
                'id' => 'required|integer|exists:usuario,id',
                'nome' => 'required|string|max:255',
                'email' => 'required|email',
                'senha' => 'required|string|min:8',
                'tipo' => 'required|in:Aluno,Funcionario',
            ]);

            try {

                $usuario = Usuario::find($request->id);
                
                if($usuario->email != $request->cpf){
                    $usuario_email_igual = Usuario::where('email', $request->email)->first();
                    if($usuario_email_igual){
                        return response()->json(['message' => 'Email já cadastrado', 'erro' => 's'], 200);
                    }
                }
                $usuario->nome = $request->nome;
                $usuario->email = $request->email;
                $usuario->senha = Hash::make($request->senha);
                $usuario->cpf = $request->cpf;
                $usuario->save();

                return response()->json(['message' => 'Usuário alterado com sucesso!','erro' =>'n'], 200);


            }catch (\Throwable $th) {
                
                return response()->json(['message' => 'Erro ao cadastrar usuário', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
            }

        }

        public function deletar_usuario(Request $request){
            $request->validate([
                'id' => 'required|integer|exists:usuario,id',
            ]);
            try {
                $usuario = Usuario::find($request->id);
                $usuario->delete();
                return response()->json(['message' => 'Usuário deletado com sucesso!', 'erro' => 'n'], 200);
            } catch (\Throwable $th) {
                return response()->json(['message' => 'Erro ao deletar usuário', 'erro' => 's', 'msg_erro' => $th->getMessage()], 200);
            }
        }
    
}
