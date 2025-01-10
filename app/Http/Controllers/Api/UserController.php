<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

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
        return response()->json([
            'user' => $request->user(),
        ], Response::HTTP_OK);
    }

    /**
     * Retorna todos os usuários (apenas para admins).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole(User::ROLE_ADMIN)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $users = User::all();

        return response()->json([
            'users' => $users,
        ], Response::HTTP_OK);
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

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * Atualiza os dados de um usuário específico (apenas para admins).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request, $id)
    {
        $requestUser = $request->user();

        if (!$requestUser->hasRole(User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:' . User::ROLE_ADMIN . ',' . User::ROLE_USER,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->fill($request->only(['name', 'email', 'role']));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ], Response::HTTP_OK);
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

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], Response::HTTP_OK);
    }

    /**
     * Exclui um usuário específico (apenas para admins).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyUser(Request $request, $id)
    {
        $requestUser = $request->user();

        if (!$requestUser->hasRole(User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $user = User::findOrFail($id);

        if ($user->id === $requestUser->id) {
            return response()->json(['message' => 'You cannot delete your own account through this method'], Response::HTTP_FORBIDDEN);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], Response::HTTP_OK);
    }
}

