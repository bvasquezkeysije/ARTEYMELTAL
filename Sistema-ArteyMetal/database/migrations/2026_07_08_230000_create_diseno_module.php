<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permisos = [
            ['nombre' => 'Ver diseno', 'slug' => 'diseno.ver'],
            ['nombre' => 'Gestionar diseno', 'slug' => 'diseno.gestionar'],
        ];

        foreach ($permisos as $permiso) {
            DB::table('permisos')->updateOrInsert(
                ['slug' => $permiso['slug']],
                ['nombre' => $permiso['nombre'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $permisoIds = DB::table('permisos')->whereIn('slug', ['diseno.ver', 'diseno.gestionar'])->pluck('id', 'slug');

        $roles = DB::table('roles')->whereIn('nombre', ['administrador', 'disenador'])->get();

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
        $permisosSlugs = ['diseno.ver', 'diseno.gestionar'];
        $permisoIds = DB::table('permisos')->whereIn('slug', $permisosSlugs)->pluck('id');

        DB::table('permiso_rol')->whereIn('permiso_id', $permisoIds)->delete();
        DB::table('permisos')->whereIn('slug', $permisosSlugs)->delete();
    }
};
