<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoAlmacen extends Model
{
    protected $table = 'movimientos_almacen';

    protected $fillable = [
        'producto_id',
        'tipo',
        'cantidad',
        'stock_resultante',
        'concepto',
        'pedido_id',
        'usuario_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_resultante' => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
