<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido_productos', function (Blueprint $table) {
            $table->integer('cantidad_recoge')->nullable()->after('cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('pedido_productos', function (Blueprint $table) {
            $table->dropColumn('cantidad_recoge');
        });
    }
};
