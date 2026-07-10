<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_producto_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_producto_id')->constrained('pedido_productos')->cascadeOnDelete();
            $table->string('archivo_path');
            $table->string('nombre_original');
            $table->string('mime_type')->nullable();
            $table->integer('tamano_bytes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_producto_archivos');
    }
};
