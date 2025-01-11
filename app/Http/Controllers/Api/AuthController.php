<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Registra um novo usuário.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_USER,
            ],
            [
                'name.required' => 'O nome é obrigatório.',
                'email.required' => 'O e-mail é obrigatório.',
                'email.unique' => 'O e-mail já está em uso.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
                'password.confirmed' => 'A confirmação da senha não corresponde.',
                'role.required' => 'O papel do usuário é obrigatório.',
                'role.in' => 'O papel informado é inválido.',
            ]
        );

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Usuário registrado com sucesso.',
            'user' => $user,
        ], 201);
    }

    /**
     * Realiza o login do usuário.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validação dos dados de entrada
        $credentials = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Informe um e-mail válido.',
                'password.required' => 'A senha é obrigatória.',
            ]
        );

        // Busca o usuário pelo email
        $user = User::where('email', $credentials['email'])->first();

        // Verifica se o usuário existe e se a senha está correta
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Gera um token de autenticação
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user, // Opcional: Retorna os dados do usuário autenticado
        ], 200);
    }

    /**
     * Realiza o logout do usuário.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoga todos os tokens do usuário
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.'], 200);
    }
}
