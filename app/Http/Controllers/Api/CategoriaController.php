<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriaController extends Controller
{
    /**
     * Listar todas as categorias.
     */
    public function index()
    {
        $categorias = Categoria::all();
        return response()->json($categorias);
    }

    /**
     * Criar uma nova categoria.
     */
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'nome' => 'required|string|max:100',
        ]);

        // Cria a categoria no banco de dados
        $categoria = Categoria::create($request->only('nome'));

        // Retorna a categoria criada com status 201 (Created)
        return response()->json($categoria, Response::HTTP_CREATED);
    }

    /**
     * Visualizar uma categoria específica.
     */
    public function show(string $id)
    {
        // Busca a categoria pelo ID
        $categoria = Categoria::find($id);

        // Se a categoria não for encontrada, retorna um erro 404
        if (!$categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], Response::HTTP_NOT_FOUND);
        }

        // Retorna a categoria encontrada
        return response()->json($categoria);
    }

    /**
     * Atualizar uma categoria existente.
     */
    public function update(Request $request, string $id)
    {
        // Busca a categoria pelo ID
        $categoria = Categoria::find($id);

        // Se a categoria não for encontrada, retorna um erro 404
        if (!$categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], Response::HTTP_NOT_FOUND);
        }

        // Validação dos dados de entrada
        $request->validate([
            'nome' => 'sometimes|string|max:100',
        ]);

        // Atualiza a categoria com os dados fornecidos
        $categoria->update($request->only('nome'));

        // Retorna a categoria atualizada
        return response()->json($categoria);
    }

    /**
     * Excluir uma categoria.
     */
    public function destroy(string $id)
    {
        // Busca a categoria pelo ID
        $categoria = Categoria::find($id);

        // Se a categoria não for encontrada, retorna um erro 404
        if (!$categoria) {
            return response()->json(['message' => 'Categoria não encontrada'], Response::HTTP_NOT_FOUND);
        }

        // Exclui a categoria
        $categoria->delete();

        // Retorna uma resposta vazia com status 204 (No Content)
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
