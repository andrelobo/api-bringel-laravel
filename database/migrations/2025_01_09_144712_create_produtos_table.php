<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id(); // Coluna ID (chave primária)
            $table->string('nome', 50); // Nome (máximo de 50 caracteres)
            $table->string('descricao', 200)->nullable(); // Descrição (máximo de 200 caracteres, opcional)
            $table->decimal('preco', 8, 2); // Preço (valor positivo, double)
            $table->date('data_validade'); // Data de validade (não pode ser anterior à data atual)
            $table->string('imagem')->nullable(); // Nome único do arquivo de imagem (opcional)
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade'); // Relacionamento com a tabela categorias
            $table->timestamps(); // Colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
