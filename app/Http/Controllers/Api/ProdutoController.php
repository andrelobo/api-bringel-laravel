<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    /**
     * Listar todos os produtos.
     */
    public function index()
    {
        $produtos = Produto::with('categoria')->get();

        return response()->json($produtos);
    }

    /**
     * Criar um novo produto.
     */
    public function store(Request $request)
    {
        // Verifica se o usuário tem permissão de admin
        if (!$request->user()->hasRole(User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Acesso não autorizado.'], Response::HTTP_FORBIDDEN);
        }

        // Validação dos dados de entrada
        $validated = $request->validate([
            'nome' => 'required|string|max:50',
            'descricao' => 'nullable|string|max:200',
            'preco' => 'required|numeric|min:0',
            'data_validade' => 'required|date|after_or_equal:today',
            'imagem' => 'nullable|image',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        // Salva a imagem, se fornecida
        if ($request->hasFile('imagem')) {
            $validated['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        // Cria o produto no banco de dados
        $produto = Produto::create($validated);

        return response()->json($produto, Response::HTTP_CREATED);
    }

    /**
     * Visualizar um produto específico.
     */
    public function show(string $id)
    {
        $produto = Produto::with('categoria')->find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($produto);
    }

    /**
     * Atualizar um produto existente.
     */
    public function update(Request $request, string $id)
    {
        // Verifica se o usuário tem permissão de admin
        if (!$request->user()->hasRole(User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Acesso não autorizado.'], Response::HTTP_FORBIDDEN);
        }

        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        // Validação dos dados de entrada
        $validated = $request->validate([
            'nome' => 'sometimes|string|max:50',
            'descricao' => 'nullable|string|max:200',
            'preco' => 'sometimes|numeric|min:0',
            'data_validade' => 'sometimes|date|after_or_equal:today',
            'imagem' => 'nullable|image',
            'categoria_id' => 'sometimes|exists:categorias,id',
        ]);

        // Atualiza a imagem, se fornecida
        if ($request->hasFile('imagem')) {
            if ($produto->imagem && Storage::disk('public')->exists($produto->imagem)) {
                Storage::disk('public')->delete($produto->imagem);
            }

            $validated['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        // Atualiza o produto
        $produto->update($validated);

        return response()->json($produto);
    }

    /**
     * Excluir um produto.
     */
    public function destroy(Request $request, string $id)
    {
        // Verifica se o usuário tem permissão de admin
        if (!$request->user()->hasRole(User::ROLE_ADMIN)) {
            return response()->json(['message' => 'Acesso não autorizado.'], Response::HTTP_FORBIDDEN);
        }

        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        // Remove a imagem, se existir
        if ($produto->imagem && Storage::disk('public')->exists($produto->imagem)) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto deletado com sucesso.'], Response::HTTP_NO_CONTENT);
    }
}
