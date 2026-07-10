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
        Schema::table('pedido_diseno_archivos', function (Blueprint $table) {
            $table->string('tipo', 20)->default('cliente')->after('pedido_id');
        });
    }

    public function down(): void
    {
        Schema::table('pedido_diseno_archivos', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
