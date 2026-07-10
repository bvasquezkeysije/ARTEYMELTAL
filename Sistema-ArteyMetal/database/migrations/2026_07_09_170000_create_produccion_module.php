<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ahora = now();

        $permisos = [
            ['nombre' => 'Ver produccion', 'slug' => 'produccion.ver', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Gestionar produccion', 'slug' => 'produccion.gestionar', 'created_at' => $ahora, 'updated_at' => $ahora],
        ];

        DB::table('permisos')->insertOrIgnore($permisos);

        $admin = DB::table('roles')->where('nombre', 'administrador')->value('id');
        $orfebre = DB::table('roles')->where('nombre', 'orfebre')->value('id');

        $permisoIds = DB::table('permisos')->whereIn('slug', ['produccion.ver', 'produccion.gestionar'])->pluck('id', 'slug');

        if ($admin) {
            foreach ($permisoIds as $permisoId) {
                DB::table('permiso_rol')->insertOrIgnore([
                    'rol_id' => $admin,
                    'permiso_id' => $permisoId,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        }

        if ($orfebre) {
            foreach ($permisoIds as $permisoId) {
                DB::table('permiso_rol')->insertOrIgnore([
                    'rol_id' => $orfebre,
                    'permiso_id' => $permisoId,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('permisos')->whereIn('slug', ['produccion.ver', 'produccion.gestionar'])->delete();
    }
};
