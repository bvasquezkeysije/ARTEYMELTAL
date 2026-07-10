
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'codigo',
        'tipo_venta',
        'pedido_id',
        'cliente_nombre',
        'fecha_venta',
        'monto_total',
        'monto_cobrado',
        'estado_pago',
        'metodo_pago',
        'vuelto',
        'monto_efectivo',
        'monto_digital',
        'observaciones',
        'usuario_id',
        'caja_apertura_id',
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'monto_total' => 'decimal:2',
        'monto_cobrado' => 'decimal:2',
        'monto_efectivo' => 'decimal:2',
        'monto_digital' => 'decimal:2',
        'vuelto' => 'decimal:2',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function comprobante(): HasOne
    {
        return $this->hasOne(ComprobanteVenta::class, 'venta_id');
    }

    public function cajaApertura(): BelongsTo
    {
        return $this->belongsTo(CajaApertura::class, 'caja_apertura_id');
    }
}
