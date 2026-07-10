<?php

namespace Database\Seeders;

use App\Models\CategoriaProducto;
use Illuminate\Database\Seeder;

class CategoriaProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['slug' => 'medallas', 'nombre' => 'Medallas', 'activo' => true],
            ['slug' => 'marbetes_distintivos', 'nombre' => 'Marbetes y Distintivos', 'activo' => true],
            ['slug' => 'placas', 'nombre' => 'Placas', 'activo' => true],
            ['slug' => 'reconocimientos', 'nombre' => 'Reconocimientos', 'activo' => true],
        ];

        CategoriaProducto::query()->upsert(
            $categorias,
            ['slug'],
            ['nombre', 'activo']
        );
    }
}

