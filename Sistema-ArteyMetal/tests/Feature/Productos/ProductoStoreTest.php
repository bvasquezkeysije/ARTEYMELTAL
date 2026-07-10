<?php

namespace Tests\Feature\Productos;

use App\Models\CategoriaProducto;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoStoreTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioConPermisoGestionarProductos(): User
    {
        $permisoVer = Permiso::firstOrCreate([
            'slug' => 'productos.ver',
        ], [
            'nombre' => 'Ver productos',
        ]);

        $permisoGestionar = Permiso::firstOrCreate([
            'slug' => 'productos.gestionar',
        ], [
            'nombre' => 'Gestionar productos',
        ]);

        $rol = Rol::firstOrCreate([
            'nombre' => 'admin-test',
        ], [
            'descripcion' => 'Rol de prueba',
            'activo' => true,
        ]);

        $rol->permisos()->sync([$permisoVer->id, $permisoGestionar->id]);

        return User::factory()->create([
            'rol_id' => $rol->id,
            'activo' => true,
        ]);
    }

    public function test_producto_se_registra_correctamente(): void
    {
        $usuario = $this->usuarioConPermisoGestionarProductos();

        CategoriaProducto::firstOrCreate([
            'slug' => 'medallas',
        ], [
            'nombre' => 'Medallas',
            'activo' => true,
        ]);

        $response = $this
            ->actingAs($usuario)
            ->post('/productos', [
                'nombre' => 'Producto Test',
                'categoria' => 'medallas',
                'descripcion' => 'Descripcion test',
                'precio_referencia' => 25.50,
                'stock_actual' => 9,
                'activo' => '1',
            ]);

        $response->assertRedirect('/productos');
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Test',
            'categoria' => 'medallas',
            'stock_actual' => 9,
        ]);
    }

    public function test_producto_rechaza_stock_negativo(): void
    {
        $usuario = $this->usuarioConPermisoGestionarProductos();

        CategoriaProducto::firstOrCreate([
            'slug' => 'medallas',
        ], [
            'nombre' => 'Medallas',
            'activo' => true,
        ]);

        $response = $this
            ->actingAs($usuario)
            ->from('/productos/create')
            ->post('/productos', [
                'nombre' => 'Producto Invalido',
                'categoria' => 'medallas',
                'descripcion' => 'Descripcion test',
                'precio_referencia' => 25.50,
                'stock_actual' => -1,
                'activo' => '1',
            ]);

        $response->assertRedirect('/productos/create');
        $response->assertSessionHasErrors('stock_actual');
    }
}
