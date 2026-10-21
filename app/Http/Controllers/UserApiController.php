<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'digits:11', 'unique:users,cpf'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create($data);

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso.',
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'cpf' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        $cpf = preg_replace('/\\D+/', '', $credentials['cpf']);
        $user = User::query()->where('cpf', $cpf)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'CPF ou senha inválidos.'], 401);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(['message' => 'Sessão encerrada.']);
    }

    public function index(): JsonResponse
    {
        return response()->json(['users' => User::query()->latest()->paginate(15)]);
    }

    public function simple(): JsonResponse
    {
        return response()->json(['users' => User::query()->select('id', 'name', 'cpf')->orderBy('name')->get()]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['user' => $user]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'cpf' => ['required', 'digits:11', Rule::unique('users', 'cpf')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'Usuário atualizado com sucesso.', 'user' => $user->fresh()]);
    }

    public function destroy(User $user): JsonResponse
    {
        abort_if(auth()->user()->is($user), 403, 'Não é permitido excluir a própria conta.');

        $user->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso.']);
    }
}
