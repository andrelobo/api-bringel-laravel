<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProdutoController;
use App\Http\Controllers\Api\UserController;

// Rotas públicas de autenticação
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Rota para obter informações do usuário autenticado
Route::get('/user/me', [UserController::class, 'me'])->name('user.me');

// Rotas que requerem autenticação
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::put('/user/update', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete', [UserController::class, 'destroy'])->name('user.delete');

    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::get('/categorias/{id}', [CategoriaController::class, 'show']);
    Route::get('/produtos', [ProdutoController::class, 'index']);
    Route::get('/produtos/{id}', [ProdutoController::class, 'show']);
});

// Rotas que requerem autenticação e role de admin
Route::middleware(['auth:sanctum', 'check.admin'])->group(function () {
    Route::get('/users', [UserController::class, 'getAll'])->name('user.index');
    Route::put('/users/{id}', [UserController::class, 'updateUser'])->name('user.update.admin');
    Route::delete('/users/{id}', [UserController::class, 'destroyUser'])->name('user.delete.admin');

    Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);
    Route::apiResource('produtos', ProdutoController::class)->except(['index', 'show']);
});

