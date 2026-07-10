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
        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedInteger('stock_tienda')->default(0)->after('stock_actual');
            $table->unsignedInteger('stock_almacen')->default(0)->after('stock_tienda');
        });

        DB::statement('UPDATE productos SET stock_almacen = stock_actual');
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['stock_tienda', 'stock_almacen']);
        });
    }
};
