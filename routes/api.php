<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');

// Rotas Protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Rota para obter dados do usuário autenticado (método 'me' no UserController)
    Route::get('/user/me', [UserController::class, 'me'])->name('user.me');

    // Rota para atualizar dados do usuário
    Route::put('/user/update', [UserController::class, 'update'])->name('user.update');

    // Rota para excluir a conta do usuário
    Route::delete('/user/delete', [UserController::class, 'destroy'])->name('user.delete');
});

// Rotas Protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Rotas de CRUD para Categorias
    Route::apiResource('categorias', CategoriaController::class);

    // Rotas de CRUD para Produtos
    Route::apiResource('produtos', ProdutoController::class);

    // Rota para obter dados do usuário autenticado
    Route::get('/user/me', [UserController::class, 'me'])->middleware('auth:sanctum')->name('user.me');});
