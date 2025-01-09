<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Retorna os dados do usuário autenticado.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        // Retorna os dados do usuário autenticado
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }

    /**
     * Atualiza os dados do usuário autenticado.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validação dos dados de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        // Retorna erros de validação, se houver
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Atualiza os dados do usuário
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        // Salva as alterações no banco de dados
        $user->save();

        // Retorna uma resposta de sucesso
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ], 200);
    }

    /**
     * Exclui a conta do usuário autenticado.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Revoga todos os tokens do usuário
        $user->tokens()->delete();

        // Exclui o usuário do banco de dados
        $user->delete();

        // Retorna uma mensagem de sucesso
        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }
}
