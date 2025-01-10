<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome'];

    /**
     * Indica se as colunas de timestamp devem ser usadas.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the produtos for the categoria.
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}

