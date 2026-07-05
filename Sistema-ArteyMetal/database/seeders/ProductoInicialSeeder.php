<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoInicialSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['codigo' => 'PROD-0001', 'nombre' => 'Medalla dorada 1er puesto', 'categoria' => 'medallas', 'descripcion' => 'Medalla para primer puesto con cinta tricolor', 'precio_referencia' => 18.00, 'stock_actual' => 40, 'activo' => true],
            ['codigo' => 'PROD-0002', 'nombre' => 'Medalla plateada 2do puesto', 'categoria' => 'medallas', 'descripcion' => 'Medalla para segundo puesto con cinta tricolor', 'precio_referencia' => 16.00, 'stock_actual' => 40, 'activo' => true],
            ['codigo' => 'PROD-0003', 'nombre' => 'Medalla bronce 3er puesto', 'categoria' => 'medallas', 'descripcion' => 'Medalla para tercer puesto con cinta tricolor', 'precio_referencia' => 15.00, 'stock_actual' => 40, 'activo' => true],
            ['codigo' => 'PROD-0004', 'nombre' => 'Medalla bronce bano oro', 'categoria' => 'medallas', 'descripcion' => 'Medalla de bronce con bano en oro', 'precio_referencia' => 24.00, 'stock_actual' => 25, 'activo' => true],
            ['codigo' => 'PROD-0005', 'nombre' => 'Medalla bronce bano plata', 'categoria' => 'medallas', 'descripcion' => 'Medalla de bronce con bano en plata', 'precio_referencia' => 23.00, 'stock_actual' => 25, 'activo' => true],
            ['codigo' => 'PROD-0006', 'nombre' => 'Marbete personalizado metal', 'categoria' => 'marbetes_distintivos', 'descripcion' => 'Marbete personalizado para eventos', 'precio_referencia' => 12.00, 'stock_actual' => 80, 'activo' => true],
            ['codigo' => 'PROD-0007', 'nombre' => 'Marbete institucional', 'categoria' => 'marbetes_distintivos', 'descripcion' => 'Marbete institucional con acabado cepillado', 'precio_referencia' => 14.00, 'stock_actual' => 60, 'activo' => true],
            ['codigo' => 'PROD-0008', 'nombre' => 'Distintivo metalico premium', 'categoria' => 'marbetes_distintivos', 'descripcion' => 'Distintivo para personal o delegaciones', 'precio_referencia' => 19.00, 'stock_actual' => 50, 'activo' => true],
            ['codigo' => 'PROD-0009', 'nombre' => 'Portadiploma con placa', 'categoria' => 'marbetes_distintivos', 'descripcion' => 'Portadiploma con placa de reconocimiento', 'precio_referencia' => 45.00, 'stock_actual' => 20, 'activo' => true],
            ['codigo' => 'PROD-0010', 'nombre' => 'Placa recordatoria bronce', 'categoria' => 'placas', 'descripcion' => 'Placa recordatoria en bronce grabado', 'precio_referencia' => 180.00, 'stock_actual' => 12, 'activo' => true],
            ['codigo' => 'PROD-0011', 'nombre' => 'Placa marmol grabada', 'categoria' => 'placas', 'descripcion' => 'Placa en marmol para develaciones', 'precio_referencia' => 220.00, 'stock_actual' => 8, 'activo' => true],
            ['codigo' => 'PROD-0012', 'nombre' => 'Placa granito grabada', 'categoria' => 'placas', 'descripcion' => 'Placa en granito para exteriores', 'precio_referencia' => 240.00, 'stock_actual' => 8, 'activo' => true],
            ['codigo' => 'PROD-0013', 'nombre' => 'Placa porcelanato grabada', 'categoria' => 'placas', 'descripcion' => 'Placa en porcelanato alta resistencia', 'precio_referencia' => 210.00, 'stock_actual' => 10, 'activo' => true],
            ['codigo' => 'PROD-0014', 'nombre' => 'Placa vidrio grabado', 'categoria' => 'placas', 'descripcion' => 'Placa de vidrio con grabado laser', 'precio_referencia' => 195.00, 'stock_actual' => 14, 'activo' => true],
            ['codigo' => 'PROD-0015', 'nombre' => 'Placa aluminio fundido', 'categoria' => 'placas', 'descripcion' => 'Placa en aluminio fundido para obras', 'precio_referencia' => 260.00, 'stock_actual' => 6, 'activo' => true],
            ['codigo' => 'PROD-0016', 'nombre' => 'Reconocimiento vidrio grabado', 'categoria' => 'reconocimientos', 'descripcion' => 'Trofeo de vidrio grabado para ceremonia', 'precio_referencia' => 95.00, 'stock_actual' => 30, 'activo' => true],
            ['codigo' => 'PROD-0017', 'nombre' => 'Sheriff personalizado', 'categoria' => 'reconocimientos', 'descripcion' => 'Placa tipo sheriff con diseno personalizado', 'precio_referencia' => 35.00, 'stock_actual' => 25, 'activo' => true],
            ['codigo' => 'PROD-0018', 'nombre' => 'Trabajo metal y vidrio', 'categoria' => 'reconocimientos', 'descripcion' => 'Reconocimiento combinado metal y vidrio', 'precio_referencia' => 120.00, 'stock_actual' => 18, 'activo' => true],
        ];

        Producto::upsert(
            $productos,
            ['codigo'],
            ['nombre', 'categoria', 'descripcion', 'precio_referencia', 'stock_actual', 'activo']
        );
    }
}
