<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_diseno_archivos', function (Blueprint $table) {
            $table->foreignId('pedido_producto_id')->nullable()->constrained('pedido_productos')->cascadeOnDelete()->after('pedido_id');
        });
    }

    public function down(): void
    {
        Schema::table('pedido_diseno_archivos', function (Blueprint $table) {
            $table->dropForeign(['pedido_producto_id']);
            $table->dropColumn('pedido_producto_id');
        });
    }
};
