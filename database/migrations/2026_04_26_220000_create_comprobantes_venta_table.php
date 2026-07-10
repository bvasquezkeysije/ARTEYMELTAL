<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->string('tipo_comprobante', 20); // boleta, factura
            $table->string('serie', 10); // B001, F001
            $table->unsignedInteger('correlativo');
            $table->string('codigo', 30)->unique(); // B001-000001
            $table->string('documento_cliente', 20)->nullable();
            $table->string('nombre_cliente', 160);
            $table->string('direccion_cliente')->nullable();
            $table->timestamps();

            $table->unique(['tipo_comprobante', 'serie', 'correlativo'], 'uq_comprobante_tipo_serie_corr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes_venta');
    }
};
