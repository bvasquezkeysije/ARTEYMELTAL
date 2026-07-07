<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rol_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            $table->boolean('activo')->default(true)->after('rol_id');
        });

        $rolAdminId = DB::table('roles')->where('nombre', 'administrador')->value('id');
        if ($rolAdminId) {
            DB::table('users')
                ->whereNull('rol_id')
                ->update(['rol_id' => $rolAdminId, 'activo' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rol_id');
            $table->dropColumn('activo');
        });
    }
};

