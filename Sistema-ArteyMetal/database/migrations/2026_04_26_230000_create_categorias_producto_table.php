<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_producto', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('nombre', 120);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('categorias_producto')->insert([
            ['slug' => 'medallas', 'nombre' => 'Medallas', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'marbetes_distintivos', 'nombre' => 'Marbetes y Distintivos', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'placas', 'nombre' => 'Placas', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'reconocimientos', 'nombre' => 'Reconocimientos', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Asegura categorias existentes historicas que ya esten en productos
        $slugsEnProductos = DB::table('productos')->select('categoria')->distinct()->pluck('categoria');
        foreach ($slugsEnProductos as $slugRaw) {
            $slug = trim((string) $slugRaw);
            if ($slug === '') {
                continue;
            }

            $existe = DB::table('categorias_producto')->where('slug', $slug)->exists();
            if ($existe) {
                continue;
            }

            DB::table('categorias_producto')->insert([
                'slug' => $slug,
                'nombre' => ucwords(str_replace('_', ' ', $slug)),
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_producto');
    }
};
