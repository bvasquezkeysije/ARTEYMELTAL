<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'nombre',
        'categoria',
        'descripcion',
        'precio_referencia',
        'stock_actual',
        'activo',
    ];

    protected $casts = [
        'precio_referencia' => 'decimal:2',
        'stock_actual' => 'integer',
        'activo' => 'boolean',
    ];

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class);
    }
}
