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
        DB::statement("UPDATE caja_aperturas SET nombre = 'Caja #' || id WHERE nombre IS NULL");

        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->string('nombre', 100)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->string('nombre', 100)->nullable()->change();
        });
    }
};
