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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre_cliente');
            $table->string('telefono_cliente', 20)->nullable();
            $table->string('tipo_producto');
            $table->text('detalle_trabajo')->nullable();
            $table->unsignedInteger('cantidad')->default(1);
            $table->string('estado', 30)->default('registrado');
            $table->date('fecha_entrega_compromiso')->nullable();
            $table->decimal('monto_total', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
