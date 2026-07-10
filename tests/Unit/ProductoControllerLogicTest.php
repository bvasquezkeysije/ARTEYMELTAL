<?php

namespace Tests\Unit;

use App\Http\Controllers\ProductoController;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class ProductoControllerLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_generar_codigo_producto_inicia_en_prod_0001(): void
    {
        $controller = new ProductoController();
        $method = new ReflectionMethod(ProductoController::class, 'generarCodigoProducto');
        $method->setAccessible(true);

        $codigo = $method->invoke($controller);

        $this->assertSame('PROD-0001', $codigo);
    }

    public function test_generar_codigo_producto_incrementa_desde_el_ultimo_registro(): void
    {
        CategoriaProducto::firstOrCreate([
            'slug' => 'medallas',
        ], [
            'nombre' => 'Medallas',
            'activo' => true,
        ]);

        Producto::create([
            'codigo' => 'PROD-0007',
            'nombre' => 'Producto base',
            'categoria' => 'medallas',
            'descripcion' => 'Base',
            'precio_referencia' => 10.50,
            'stock_actual' => 5,
            'activo' => true,
        ]);

        $controller = new ProductoController();
        $method = new ReflectionMethod(ProductoController::class, 'generarCodigoProducto');
        $method->setAccessible(true);

        $codigo = $method->invoke($controller);

        $this->assertSame('PROD-0008', $codigo);
    }
}
