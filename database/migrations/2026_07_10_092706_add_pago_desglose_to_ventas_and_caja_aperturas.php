<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('monto_efectivo', 10, 2)->default(0)->after('vuelto');
            $table->decimal('monto_digital', 10, 2)->default(0)->after('monto_efectivo');
        });

        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->decimal('total_efectivo', 12, 2)->default(0)->after('total_ventas');
            $table->decimal('total_digital', 12, 2)->default(0)->after('total_efectivo');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('monto_efectivo');
            $table->dropColumn('monto_digital');
        });

        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->dropColumn('total_efectivo');
            $table->dropColumn('total_digital');
        });
    }
};
