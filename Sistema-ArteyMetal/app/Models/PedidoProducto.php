<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PedidoProducto extends Model
{
    protected $table = 'pedido_productos';

    protected $fillable = [
        'pedido_id',
        'nombre',
        'descripcion',
        'precio_unitario',
        'cantidad',
        'total',
        'orden',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(PedidoProductoArchivo::class, 'pedido_producto_id');
    }

    public function archivosCliente(): HasMany
    {
        return $this->hasMany(PedidoProductoArchivo::class, 'pedido_producto_id')->where('tipo', 'cliente');
    }

    public function archivosDisenador(): HasMany
    {
        return $this->hasMany(PedidoProductoArchivo::class, 'pedido_producto_id')->where('tipo', 'disenador');
    }
}
