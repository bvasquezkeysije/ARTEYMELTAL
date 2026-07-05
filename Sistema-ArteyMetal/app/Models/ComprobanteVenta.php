<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComprobanteVenta extends Model
{
    protected $table = 'comprobantes_venta';

    protected $fillable = [
        'venta_id',
        'tipo_comprobante',
        'serie',
        'correlativo',
        'codigo',
        'documento_cliente',
        'nombre_cliente',
        'direccion_cliente',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
