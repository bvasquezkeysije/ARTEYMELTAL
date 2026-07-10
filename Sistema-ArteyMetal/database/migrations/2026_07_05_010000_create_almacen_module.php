<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_almacen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('tipo', 20); // entrada / salida
            $table->integer('cantidad');
            $table->integer('stock_resultante');
            $table->string('concepto', 255)->nullable();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });

        $permisos = [
            ['nombre' => 'Ver almacen', 'slug' => 'almacen.ver'],
            ['nombre' => 'Gestionar almacen', 'slug' => 'almacen.gestionar'],
        ];

        foreach ($permisos as $permiso) {
            DB::table('permisos')->updateOrInsert(
                ['slug' => $permiso['slug']],
                ['nombre' => $permiso['nombre'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $permisoIds = DB::table('permisos')->whereIn('slug', ['almacen.ver', 'almacen.gestionar'])->pluck('id', 'slug');

        $roles = DB::table('roles')->whereIn('nombre', ['administrador', 'almacenero'])->get();

        foreach ($roles as $rol) {
            foreach ($permisoIds as $permisoId) {
                DB::table('permiso_rol')->updateOrInsert(
                    ['rol_id' => $rol->id, 'permiso_id' => $permisoId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        $permisosSlugs = ['almacen.ver', 'almacen.gestionar'];
        $permisoIds = DB::table('permisos')->whereIn('slug', $permisosSlugs)->pluck('id');

        DB::table('permiso_rol')->whereIn('permiso_id', $permisoIds)->delete();
        DB::table('permisos')->whereIn('slug', $permisosSlugs)->delete();
        Schema::dropIfExists('movimientos_almacen');
    }
};
