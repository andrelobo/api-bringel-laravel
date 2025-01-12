<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CategoriaController extends Controller
{
    /**
     * Listar todas as categorias.
     */
    public function index()
    {
        Log::info('Método index chamado - Listando todas as categorias.');

        $categorias = Categoria::all();

        return response()->json([
            'success' => true,
            'data' => $categorias,
        ], Response::HTTP_OK);
    }

    /**
     * Criar uma nova categoria.
     */
    public function store(Request $request)
    {
        Log::info('Método store chamado', ['dados_recebidos' => $request->all()]);

        try {
            // Validação dos dados recebidos
            $validated = $request->validate([
                'nome' => 'required|string|max:100',
            ]);

            // Criação da categoria
            $categoria = Categoria::create($validated);

            Log::info('Categoria criada com sucesso', ['categoria' => $categoria]);

            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso',
                'data' => $categoria,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar categoria', [
                'erro' => $e->getMessage(),
                'dados_recebidos' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar categoria',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Visualizar uma categoria específica.
     */
    public function show(string $id)
    {
        Log::info('Método show chamado', ['categoria_id' => $id]);

        $categoria = Categoria::find($id);

        if (!$categoria) {
            Log::warning('Categoria não encontrada', ['categoria_id' => $id]);

            return $this->errorResponse(
                'Categoria não encontrada',
                ['id' => ['Categoria com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json([
            'success' => true,
            'data' => $categoria,
        ], Response::HTTP_OK);
    }

    /**
     * Atualizar uma categoria existente.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Método update chamado', ['categoria_id' => $id, 'dados_recebidos' => $request->all()]);

        $categoria = Categoria::find($id);

        if (!$categoria) {
            Log::warning('Categoria não encontrada', ['categoria_id' => $id]);

            return $this->errorResponse(
                'Categoria não encontrada',
                ['id' => ['Categoria com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            // Validação dos dados
            $validated = $request->validate([
                'nome' => 'required|string|max:100',
            ]);

            // Atualização da categoria
            $categoria->update($validated);

            Log::info('Categoria atualizada com sucesso', ['categoria' => $categoria]);

            return response()->json([
                'success' => true,
                'message' => 'Categoria atualizada com sucesso',
                'data' => $categoria,
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar categoria', [
                'erro' => $e->getMessage(),
                'categoria_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar categoria',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Excluir uma categoria.
     */
    public function destroy(string $id)
    {
        Log::info('Método destroy chamado', ['categoria_id' => $id]);

        $categoria = Categoria::find($id);

        if (!$categoria) {
            Log::warning('Categoria não encontrada para exclusão', ['categoria_id' => $id]);

            return $this->errorResponse(
                'Categoria não encontrada',
                ['id' => ['Categoria com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $categoria->delete();

            Log::info('Categoria excluída com sucesso', ['categoria_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Categoria excluída com sucesso',
            ], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            Log::error('Erro ao excluir categoria', [
                'erro' => $e->getMessage(),
                'categoria_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir categoria',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
