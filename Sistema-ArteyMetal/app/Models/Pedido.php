<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_id',
        'codigo',
        'nombre_producto',
        'nombre_cliente',
        'telefono_cliente',
        'documento_cliente',
        'correo_cliente',
        'tipo_producto',
        'materiales',
        'tipo_entrega',
        'direccion_entrega',
        'referencia_entrega',
        'distrito_entrega',
        'codigo_postal_entrega',
        'nombre_recibe',
        'telefono_recibe',
        'costo_delivery',
        'detalle_trabajo',
        'cantidad',
        'estado',
        'estado_personalizacion',
        'fecha_entrega_compromiso',
        'fecha_inicio_diseno',
        'fecha_aprobacion_diseno',
        'archivo_diseno_path',
        'observaciones_personalizacion',
        'monto_total',
        'estado_pago',
        'monto_adelanto',
        'monto_saldo',
        'observaciones',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_entrega_compromiso' => 'date',
        'fecha_inicio_diseno' => 'date',
        'fecha_aprobacion_diseno' => 'date',
        'monto_total' => 'decimal:2',
        'costo_delivery' => 'decimal:2',
        'monto_adelanto' => 'decimal:2',
        'monto_saldo' => 'decimal:2',
        'materiales' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function archivosDiseno(): HasMany
    {
        return $this->hasMany(PedidoDisenoArchivo::class, 'pedido_id');
    }

    public function archivosOrden(): HasMany
    {
        return $this->hasMany(PedidoOrdenArchivo::class, 'pedido_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'pedido_id');
    }
}
