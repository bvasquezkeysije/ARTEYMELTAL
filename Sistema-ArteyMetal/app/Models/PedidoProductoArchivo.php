<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoProductoArchivo extends Model
{
    protected $table = 'pedido_producto_archivos';

    protected $fillable = [
        'pedido_producto_id',
        'tipo',
        'archivo_path',
        'nombre_original',
        'mime_type',
        'tamano_bytes',
    ];

    public function pedidoProducto(): BelongsTo
    {
        return $this->belongsTo(PedidoProducto::class);
    }
}
