<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Produto extends Model
{
    use HasFactory;

    // Nome da tabela (opcional, pois o Laravel já infere o nome da tabela)
    protected $table = 'produtos';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'data_validade',
        'imagem',
        'categoria_id',
    ];

    // Campos que devem ser convertidos para tipos específicos
    protected $casts = [
        'preco' => 'decimal:2', // Garante que o preço seja tratado como decimal com 2 casas
        'data_validade' => 'date', // Garante que a data de validade seja tratada como data
    ];

    // Relacionamento com a tabela categorias
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Acessor para retornar a URL completa da imagem
    public function getImagemUrlAttribute()
    {
        return $this->imagem ? Storage::url($this->imagem) : null;
    }

    // Mutator para garantir que o nome seja sempre capitalizado ao salvar
    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = ucfirst($value);
    }

    // Mutator para garantir que a descrição seja sempre trimada ao salvar
    public function setDescricaoAttribute($value)
    {
        $this->attributes['descricao'] = trim($value);
    }

    // Escopo local para filtrar produtos por categoria
    public function scopeDaCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // Escopo local para filtrar produtos com data de validade futura
    public function scopeValidos($query)
    {
        return $query->where('data_validade', '>=', now());
    }
}
