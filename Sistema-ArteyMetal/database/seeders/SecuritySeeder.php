<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
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

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(
                ['slug' => $permiso['slug']],
                ['nombre' => $permiso['nombre']]
            );
        }

        $permisosPorSlug = Permiso::query()->pluck('id', 'slug');

        $rolAdmin = Rol::updateOrCreate(
            ['nombre' => 'admin'],
            [
                'descripcion' => 'Acceso total al sistema',
                'activo' => true,
            ]
        );
        $rolAdmin->permisos()->sync($permisosPorSlug->values()->all());

        $rolVentas = Rol::updateOrCreate(
            ['nombre' => 'ventas'],
            [
                'descripcion' => 'Gestion comercial de clientes y ventas',
                'activo' => true,
            ]
        );
        $rolVentas->permisos()->sync(
            $this->idsPermisos($permisosPorSlug, [
                'dashboard.ver',
                'clientes.ver',
                'clientes.gestionar',
                'productos.ver',
                'ventas.ver',
                'ventas.gestionar',
                'pedidos.ver',
                'reportes.ver',
            ])
        );

        $rolOrfebre = Rol::updateOrCreate(
            ['nombre' => 'Orfebre'],
            [
                'descripcion' => 'Seguimiento y gestion de pedidos en produccion',
                'activo' => true,
            ]
        );
        $rolOrfebre->permisos()->sync(
            $this->idsPermisos($permisosPorSlug, [
                'dashboard.ver',
                'pedidos.ver',
                'pedidos.gestionar',
                'clientes.ver',
                'productos.ver',
                'reportes.ver',
            ])
        );

        $rolAlmacen = Rol::updateOrCreate(
            ['nombre' => 'almacen'],
            [
                'descripcion' => 'Control de catalogo y stock',
                'activo' => true,
            ]
        );
        $rolAlmacen->permisos()->sync(
            $this->idsPermisos($permisosPorSlug, [
                'dashboard.ver',
                'productos.ver',
                'productos.gestionar',
                'reportes.ver',
            ])
        );

        $usuariosSistema = [
            ['username' => 'bvasquezkeysije', 'password' => '76636255', 'rol_id' => $rolAdmin->id],
            ['username' => 'pfernandezadeli', 'password' => '77684878', 'rol_id' => $rolAdmin->id],
            ['username' => 'ventas', 'password' => 'ventas123', 'rol_id' => $rolVentas->id],
            ['username' => 'produccion', 'password' => 'produccion123', 'rol_id' => $rolOrfebre->id],
            ['username' => 'almacen', 'password' => 'almacen123', 'rol_id' => $rolAlmacen->id],
        ];

        foreach ($usuariosSistema as $usuario) {
            $username = $usuario['username'];
            $gmail = $username . '@gmail.com';
            $legacy = $username . '@arteymetales.online';

            $actual = User::query()
                ->where('name', $username)
                ->orWhere('email', $gmail)
                ->orWhere('email', $legacy)
                ->orderBy('id')
                ->first();

            if (! $actual) {
                $actual = new User();
            }

            $actual->fill([
                'name' => $username,
                'email' => $gmail,
                'password' => Hash::make($usuario['password']),
                'rol_id' => $usuario['rol_id'],
                'activo' => true,
            ]);
            $actual->save();

            // Si quedaron duplicados legacy, los removemos para evitar usuarios repetidos en UI.
            User::query()
                ->where('id', '!=', $actual->id)
                ->where(function ($q) use ($username, $gmail, $legacy) {
                    $q->where('name', $username)
                        ->orWhere('email', $gmail)
                        ->orWhere('email', $legacy);
                })
                ->delete();
        }
    }

    private function idsPermisos($permisosPorSlug, array $slugs): array
    {
        return collect($slugs)
            ->map(fn (string $slug) => $permisosPorSlug[$slug] ?? null)
            ->filter()
            ->values()
            ->all();
    }
}
