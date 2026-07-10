<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('caja_apertura_id')->nullable()->constrained('caja_aperturas')->nullOnDelete()->after('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['caja_apertura_id']);
            $table->dropColumn('caja_apertura_id');
        });
    }
};
