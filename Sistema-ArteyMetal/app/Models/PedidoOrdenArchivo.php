<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoOrdenArchivo extends Model
{
    protected $table = 'pedido_orden_archivos';

    protected $fillable = [
        'pedido_id',
        'archivo_path',
        'nombre_original',
        'mime_type',
        'tamano_bytes',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
