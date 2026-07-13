<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        DB::table('cajas')->insert([
            ['nombre' => 'Caja 1', 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Caja 2', 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Caja 3', 'activa' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
            $table->dropColumn('caja_id');
        });
        Schema::dropIfExists('cajas');
    }
};
