<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PedidoDisenoArchivo extends Model
{
    protected $table = 'pedido_diseno_archivos';

    protected $fillable = [
        'pedido_id',
        'tipo',
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
