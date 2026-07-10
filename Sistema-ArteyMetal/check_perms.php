<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$slugs = ['almacen.ver','almacen.gestionar'];
foreach ($slugs as $slug) {
    $p = \App\Models\Permiso::where('slug', $slug)->first();
    if ($p) {
        $roles = $p->roles->pluck('nombre')->implode(', ');
        echo "$slug: roles => " . ($roles ?: 'NINGUNO') . "\n";
    } else {
        echo "$slug: NO ENCONTRADO\n";
    }
}

echo "\n--- Rol del usuario 'ventas' ---\n";
$v = \App\Models\User::where('name', 'ventas')->first();
if ($v) {
    echo "Rol: " . ($v->rol?->nombre ?? 'SIN ROL') . "\n";
    echo "Tiene almacen.ver: " . ($v->tienePermiso('almacen.ver') ? 'SI' : 'NO') . "\n";
    echo "Tiene almacen.gestionar: " . ($v->tienePermiso('almacen.gestionar') ? 'SI' : 'NO') . "\n";
}

echo "\n--- Rol del usuario 'almacen' ---\n";
$a = \App\Models\User::where('name', 'almacen')->first();
if ($a) {
    echo "Rol: " . ($a->rol?->nombre ?? 'SIN ROL') . "\n";
    echo "Tiene almacen.ver: " . ($a->tienePermiso('almacen.ver') ? 'SI' : 'NO') . "\n";
    echo "Tiene almacen.gestionar: " . ($a->tienePermiso('almacen.gestionar') ? 'SI' : 'NO') . "\n";
}
