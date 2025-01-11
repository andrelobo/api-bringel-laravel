<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriaController extends Controller
{
    public function __construct()
    {
        // Middleware para proteger rotas administrativas
        $this->middleware('can:admin')->except(['index', 'show']);
    }

    /**
     * Listar todas as categorias.
     */
    public function index()
    {
        $categorias = Categoria::all();

        return response()->json([
            'success' => true,
            'data' => $categorias,
        ]);
    }

    /**
     * Criar uma nova categoria.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
        ]);

        $categoria = Categoria::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoria criada com sucesso',
            'data' => $categoria,
        ], Response::HTTP_CREATED);
    }

    /**
     * Visualizar uma categoria específica.
     */
    public function show(string $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return $this->errorResponse(
                'Categoria não encontrada',
                ['id' => ['Categoria com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json([
            'success' => true,
            'data' => $categoria,
        ]);
    }

    /**
     * Atualizar uma categoria existente.
     */
    public function update(Request $request, string $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return $this->errorResponse(
                'Categoria não encontrada',
                ['id' => ['Categoria com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
        ]);

        $categoria->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoria atualizada com sucesso',
            'data' => $categoria,
        ]);
    }

    /**
     * Excluir uma categoria.
     */
    public function destroy(string $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return $this->errorResponse(
                'Categoria não encontrada',
                ['id' => ['Categoria com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoria excluída com sucesso',
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Método auxiliar para padronizar respostas de erro.
     */
    private function errorResponse(string $message, array $errors = [], int $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
