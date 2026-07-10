<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_aperturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamp('fecha_apertura')->useCurrent();
            $table->decimal('monto_inicial', 12, 2)->default(0);
            $table->timestamp('fecha_cierre')->nullable();
            $table->decimal('monto_final', 12, 2)->nullable();
            $table->decimal('total_ventas', 12, 2)->default(0);
            $table->string('estado', 20)->default('abierta');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_aperturas');
    }
};
