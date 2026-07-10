<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BaseDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategoriaProductoSeeder::class,
            ProductoInicialSeeder::class,
            ClienteInicialSeeder::class,
            PedidoInicialSeeder::class,
            VentaInicialSeeder::class,
        ]);
    }
}
