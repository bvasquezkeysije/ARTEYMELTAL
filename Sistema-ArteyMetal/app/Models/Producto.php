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
        'stock_tienda',
        'stock_almacen',
        'stock_actual',
        'activo',
    ];

    protected $casts = [
        'precio_referencia' => 'decimal:2',
        'stock_tienda' => 'integer',
        'stock_almacen' => 'integer',
        'stock_actual' => 'integer',
        'activo' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (Producto $producto) {
            $producto->stock_actual = ($producto->stock_tienda ?? 0) + ($producto->stock_almacen ?? 0);
        });
    }

    public function getStockActualAttribute(): int
    {
        return $this->stock_tienda + $this->stock_almacen;
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class);
    }
}
