<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ahora = now();

        $permisos = [
            ['nombre' => 'Ver repartidor', 'slug' => 'repartidor.ver', 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Gestionar repartidor', 'slug' => 'repartidor.gestionar', 'created_at' => $ahora, 'updated_at' => $ahora],
        ];

        DB::table('permisos')->insertOrIgnore($permisos);

        $admin = DB::table('roles')->where('nombre', 'administrador')->value('id');
        $repartidor = DB::table('roles')->where('nombre', 'repartidor')->value('id');

        $permisoIds = DB::table('permisos')->whereIn('slug', ['repartidor.ver', 'repartidor.gestionar'])->pluck('id', 'slug');

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

        if ($repartidor) {
            foreach ($permisoIds as $permisoId) {
                DB::table('permiso_rol')->insertOrIgnore([
                    'rol_id' => $repartidor,
                    'permiso_id' => $permisoId,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('permisos')->whereIn('slug', ['repartidor.ver', 'repartidor.gestionar'])->delete();
    }
};
