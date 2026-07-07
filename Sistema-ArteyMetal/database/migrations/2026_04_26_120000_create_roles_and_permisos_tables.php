<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->string('descripcion', 200)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        Schema::create('permiso_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['rol_id', 'permiso_id']);
        });

        $permisos = [
            ['nombre' => 'Ver inicio', 'slug' => 'dashboard.ver'],
            ['nombre' => 'Ver pedidos', 'slug' => 'pedidos.ver'],
            ['nombre' => 'Gestionar pedidos', 'slug' => 'pedidos.gestionar'],
            ['nombre' => 'Ver clientes', 'slug' => 'clientes.ver'],
            ['nombre' => 'Gestionar clientes', 'slug' => 'clientes.gestionar'],
            ['nombre' => 'Ver productos', 'slug' => 'productos.ver'],
            ['nombre' => 'Gestionar productos', 'slug' => 'productos.gestionar'],
            ['nombre' => 'Ver ventas', 'slug' => 'ventas.ver'],
            ['nombre' => 'Gestionar ventas', 'slug' => 'ventas.gestionar'],
            ['nombre' => 'Ver reportes', 'slug' => 'reportes.ver'],
            ['nombre' => 'Ver usuarios', 'slug' => 'usuarios.ver'],
            ['nombre' => 'Gestionar usuarios', 'slug' => 'usuarios.gestionar'],
            ['nombre' => 'Ver roles', 'slug' => 'roles.ver'],
            ['nombre' => 'Gestionar roles', 'slug' => 'roles.gestionar'],
            ['nombre' => 'Ver configuracion', 'slug' => 'configuracion.ver'],
        ];

        DB::table('permisos')->insert($permisos);

        $rolAdminId = DB::table('roles')->insertGetId([
            'nombre' => 'administrador',
            'descripcion' => 'Acceso total al sistema',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permisoIds = DB::table('permisos')->pluck('id');
        $pivot = [];
        foreach ($permisoIds as $permisoId) {
            $pivot[] = [
                'rol_id' => $rolAdminId,
                'permiso_id' => $permisoId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($pivot)) {
            DB::table('permiso_rol')->insert($pivot);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permiso_rol');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};

