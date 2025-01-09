<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
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
        // Validação dos dados de entrada
        $request->validate([
            'nome' => 'required|string|max:50',
            'descricao' => 'nullable|string|max:200',
            'preco' => 'required|numeric|min:0',
            'data_validade' => 'required|date|after_or_equal:today',
            'imagem' => 'nullable|image|unique:produtos,imagem',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        // Salva a imagem (se fornecida)
        if ($request->hasFile('imagem')) {
            $imagemPath = $request->file('imagem')->store('produtos', 'public');
            $request->merge(['imagem' => $imagemPath]);
        }

        // Cria o produto no banco de dados
        $produto = Produto::create($request->all());

        // Retorna o produto criado com status 201 (Created)
        return response()->json($produto, Response::HTTP_CREATED);
    }

    /**
     * Visualizar um produto específico.
     */
    public function show(string $id)
    {
        // Busca o produto pelo ID
        $produto = Produto::with('categoria')->find($id);

        // Se o produto não for encontrado, retorna um erro 404
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Retorna o produto encontrado
        return response()->json($produto);
    }

    /**
     * Atualizar um produto existente.
     */
    public function update(Request $request, string $id)
    {
        // Busca o produto pelo ID
        $produto = Produto::find($id);

        // Se o produto não for encontrado, retorna um erro 404
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Validação dos dados de entrada
        $request->validate([
            'nome' => 'sometimes|string|max:50',
            'descricao' => 'nullable|string|max:200',
            'preco' => 'sometimes|numeric|min:0',
            'data_validade' => 'sometimes|date|after_or_equal:today',
            'imagem' => 'nullable|image|unique:produtos,imagem,' . $produto->id,
            'categoria_id' => 'sometimes|exists:categorias,id',
        ]);

        // Atualiza a imagem (se fornecida)
        if ($request->hasFile('imagem')) {
            // Remove a imagem antiga (se existir)
            if ($produto->imagem && Storage::disk('public')->exists($produto->imagem)) {
                Storage::disk('public')->delete($produto->imagem);
            }

            // Salva a nova imagem
            $imagemPath = $request->file('imagem')->store('produtos', 'public');
            $request->merge(['imagem' => $imagemPath]);
        }

        // Atualiza o produto com os dados fornecidos
        $produto->update($request->all());

        // Retorna o produto atualizado
        return response()->json($produto);
    }

    /**
     * Excluir um produto.
     */
    public function destroy(string $id)
    {
        // Busca o produto pelo ID
        $produto = Produto::find($id);

        // Se o produto não for encontrado, retorna um erro 404
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Remove a imagem (se existir)
        if ($produto->imagem && Storage::disk('public')->exists($produto->imagem)) {
            Storage::disk('public')->delete($produto->imagem);
        }

        // Exclui o produto
        $produto->delete();

        // Retorna uma resposta vazia com status 204 (No Content)
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
