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
        // Validação dos dados de entrada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Adicionado confirmação de senha
        ]);

        // Criação do usuário
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Gera um token de autenticação
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retorna a resposta com o token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user, // Retorna os dados do usuário (opcional)
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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Busca o usuário pelo email
        $user = User::where('email', $credentials['email'])->first();

        // Verifica se o usuário existe e se a senha está correta
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Gera um token de autenticação
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retorna a resposta com o token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user, // Retorna os dados do usuário (opcional)
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

        // Retorna uma mensagem de sucesso
        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}
