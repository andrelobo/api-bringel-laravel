<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    /**
     * Listar todos os produtos.
     */
    public function index()
    {
        Log::info('Método index chamado - Listando todos os produtos.');

        $produtos = Produto::with('categoria')->get();

        return response()->json([
            'success' => true,
            'data' => $produtos,
        ], Response::HTTP_OK);
    }

    /**
     * Criar um novo produto.
     */

     public function store(Request $request)
{
    // Validação dos dados recebidos
    $validated = $request->validate([
        'nome' => 'required|string|max:100',
        'descricao' => 'required|string|max:255',
        'preco' => 'required|numeric',
        'data_validade' => 'required|date',
        'imagem' => 'nullable|string|max:255', // Apenas a string do nome da imagem ou URL
        'categoria_id' => 'required|exists:categorias,id',
    ]);

    // Criação do produto
    $produto = Produto::create([
        'nome' => $validated['nome'],
        'descricao' => $validated['descricao'],
        'preco' => $validated['preco'],
        'data_validade' => $validated['data_validade'],
        'imagem' => $validated['imagem'],
        'categoria_id' => $validated['categoria_id'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Produto criado com sucesso',
        'data' => $produto,
    ], Response::HTTP_CREATED);
}

    /**
     * Visualizar um produto específico.
     */
    public function show(string $id)
    {
        Log::info('Método show chamado', ['produto_id' => $id]);

        $produto = Produto::with('categoria')->find($id);

        if (!$produto) {
            Log::warning('Produto não encontrado', ['produto_id' => $id]);

            return $this->errorResponse(
                'Produto não encontrado',
                ['id' => ['Produto com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json([
            'success' => true,
            'data' => $produto,
        ], Response::HTTP_OK);
    }

    /**
     * Atualizar um produto existente.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Método update chamado', ['produto_id' => $id, 'dados_recebidos' => $request->all()]);

        $produto = Produto::find($id);

        if (!$produto) {
            Log::warning('Produto não encontrado', ['produto_id' => $id]);

            return $this->errorResponse(
                'Produto não encontrado',
                ['id' => ['Produto com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            // Validação dos dados
            $validated = $request->validate([
                'nome' => 'required|string|max:100',
                'descricao' => 'nullable|string',
                'preco' => 'required|numeric|min:0',
                'data_validade' => 'nullable|date',
                'imagem' => 'nullable|image|max:2048', // Máx. 2 MB
                'categoria_id' => 'required|exists:categorias,id',
            ]);

            // Upload da imagem, se fornecida
            if ($request->hasFile('imagem')) {
                // Remove a imagem antiga, se existir
                if ($produto->imagem) {
                    Storage::delete($produto->imagem);
                }

                $validated['imagem'] = $request->file('imagem')->store('produtos');
            }

            // Atualização do produto
            $produto->update($validated);

            Log::info('Produto atualizado com sucesso', ['produto' => $produto]);

            return response()->json([
                'success' => true,
                'message' => 'Produto atualizado com sucesso',
                'data' => $produto,
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar produto', [
                'erro' => $e->getMessage(),
                'produto_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar produto',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Excluir um produto.
     */
    public function destroy(string $id)
    {
        Log::info('Método destroy chamado', ['produto_id' => $id]);

        $produto = Produto::find($id);

        if (!$produto) {
            Log::warning('Produto não encontrado para exclusão', ['produto_id' => $id]);

            return $this->errorResponse(
                'Produto não encontrado',
                ['id' => ['Produto com o ID fornecido não existe']],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            // Remove a imagem associada, se existir
            if ($produto->imagem) {
                Storage::delete($produto->imagem);
            }

            $produto->delete();

            Log::info('Produto excluído com sucesso', ['produto_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Produto excluído com sucesso',
            ], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            Log::error('Erro ao excluir produto', [
                'erro' => $e->getMessage(),
                'produto_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir produto',
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
