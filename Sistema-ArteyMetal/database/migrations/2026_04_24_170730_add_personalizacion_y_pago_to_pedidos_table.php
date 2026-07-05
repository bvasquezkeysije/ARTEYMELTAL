<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('estado_personalizacion', 40)->default('sin_iniciar')->after('estado');
            $table->date('fecha_inicio_diseno')->nullable()->after('fecha_entrega_compromiso');
            $table->date('fecha_aprobacion_diseno')->nullable()->after('fecha_inicio_diseno');
            $table->string('archivo_diseno_path')->nullable()->after('fecha_aprobacion_diseno');
            $table->text('observaciones_personalizacion')->nullable()->after('archivo_diseno_path');
            $table->string('estado_pago', 30)->default('pendiente_adelanto')->after('monto_total');
            $table->decimal('monto_adelanto', 10, 2)->nullable()->after('estado_pago');
            $table->decimal('monto_saldo', 10, 2)->nullable()->after('monto_adelanto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'estado_personalizacion',
                'fecha_inicio_diseno',
                'fecha_aprobacion_diseno',
                'archivo_diseno_path',
                'observaciones_personalizacion',
                'estado_pago',
                'monto_adelanto',
                'monto_saldo',
            ]);
        });
    }
};
