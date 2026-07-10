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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('tipo_entrega', 20)->default('local')->after('tipo_producto');
            $table->string('direccion_entrega')->nullable()->after('tipo_entrega');
            $table->string('referencia_entrega')->nullable()->after('direccion_entrega');
            $table->string('distrito_entrega', 120)->nullable()->after('referencia_entrega');
            $table->string('nombre_recibe', 120)->nullable()->after('distrito_entrega');
            $table->string('telefono_recibe', 20)->nullable()->after('nombre_recibe');
            $table->decimal('costo_delivery', 10, 2)->nullable()->after('telefono_recibe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_entrega',
                'direccion_entrega',
                'referencia_entrega',
                'distrito_entrega',
                'nombre_recibe',
                'telefono_recibe',
                'costo_delivery',
            ]);
        });
    }
};

