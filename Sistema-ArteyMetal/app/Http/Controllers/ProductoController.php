<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function index()
    {
        $busqueda = request('q');
        $categoria = request('categoria');
        $filtroActivo = request('activo');
        $categorias = CategoriaProducto::query()->orderBy('nombre')->get();
        $categoriasMap = $categorias->pluck('nombre', 'slug')->all();

        $productos = Producto::query()
            ->with('imagenes')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('codigo', 'like', "%{$busqueda}%")
                    ->orWhere('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('descripcion', 'like', "%{$busqueda}%");
            })
            ->when($categoria, function ($query) use ($categoria) {
                $query->where('categoria', $categoria);
            })
            ->when($filtroActivo !== null && $filtroActivo !== '', function ($query) use ($filtroActivo) {
                $query->where('activo', $filtroActivo);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('productos.index', compact('productos', 'busqueda', 'categoria', 'filtroActivo', 'categorias', 'categoriasMap'));
    }

    public function create()
    {
        $categorias = CategoriaProducto::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $datos = $this->validarProducto($request);

        DB::transaction(function () use ($request, $datos) {
            $datos['codigo'] = $this->generarCodigoProducto();
            $producto = Producto::create($datos);
            $this->guardarImagenes($request, $producto);
        });

        return redirect()->route('productos.index')->with('ok', 'Producto registrado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load('imagenes');

        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $producto->load('imagenes');

        $categorias = CategoriaProducto::query()
            ->where('activo', true)
            ->orWhere('slug', $producto->categoria)
            ->orderBy('nombre')
            ->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $this->validarProducto($request);

        DB::transaction(function () use ($request, $producto, $datos) {
            $producto->update($datos);
            $this->guardarImagenes($request, $producto);
        });

        return redirect()->route('productos.index')->with('ok', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->load('imagenes');

        foreach ($producto->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->archivo_path);
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('ok', 'Producto eliminado correctamente.');
    }

    public function destroyImagen(Producto $producto, ProductoImagen $imagen)
    {
        if ((int) $imagen->producto_id !== (int) $producto->id) {
            return response()->json(['ok' => false, 'message' => 'Imagen no corresponde al producto.'], 422);
        }

        Storage::disk('public')->delete($imagen->archivo_path);
        $imagen->delete();

        return response()->json(['ok' => true, 'message' => 'Imagen eliminada.']);
    }

    public function verImagen(ProductoImagen $imagen)
    {
        $ruta = $this->resolverRutaImagen($imagen->archivo_path);

        if (! $ruta || ! Storage::disk('public')->exists($ruta)) {
            abort(404, 'Imagen no encontrada.');
        }

        return response()->file(Storage::disk('public')->path($ruta), [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function validarProducto(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'categoria' => ['required', 'string', 'max:60', 'exists:categorias_producto,slug'],
            'descripcion' => ['nullable', 'string'],
            'precio_referencia' => ['nullable', 'numeric', 'min:0'],
            'stock_actual' => ['required', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
            'imagenes' => ['nullable', 'array'],
            'imagenes.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'imagenes.*.uploaded' => 'La imagen no se pudo subir. Verifica que no supere los 10 MB.',
            'imagenes.*.max' => 'Cada imagen debe pesar como maximo 10 MB.',
            'imagenes.*.mimes' => 'Solo se permiten imagenes JPG, JPEG, PNG o WEBP.',
            'imagenes.*.image' => 'El archivo seleccionado debe ser una imagen valida.',
        ]);
    }

    private function generarCodigoProducto(): string
    {
        $ultimoCodigo = Producto::query()
            ->where('codigo', 'like', 'PROD-%')
            ->orderByDesc('id')
            ->value('codigo');

        $numero = 0;
        if ($ultimoCodigo && preg_match('/^PROD-(\d+)$/', $ultimoCodigo, $coincidencias)) {
            $numero = (int) $coincidencias[1];
        }

        do {
            $numero++;
            $codigo = 'PROD-' . str_pad((string) $numero, 4, '0', STR_PAD_LEFT);
        } while (Producto::query()->where('codigo', $codigo)->exists());

        return $codigo;
    }

    public function categoriasJson()
    {
        $categorias = CategoriaProducto::query()
            ->orderBy('nombre')
            ->get(['id', 'slug', 'nombre', 'activo']);

        return response()->json(['ok' => true, 'categorias' => $categorias]);
    }

    public function categoriaStore(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
        ]);

        $slugBase = Str::slug($datos['nombre'], '_');
        $slug = $slugBase !== '' ? $slugBase : 'categoria';
        $i = 1;
        while (CategoriaProducto::query()->where('slug', $slug)->exists()) {
            $i++;
            $slug = $slugBase.'_'.$i;
        }

        $categoria = CategoriaProducto::create([
            'nombre' => trim($datos['nombre']),
            'slug' => $slug,
            'activo' => true,
        ]);

        return response()->json(['ok' => true, 'message' => 'Categoria creada.', 'categoria' => $categoria]);
    }

    public function categoriaUpdate(Request $request, CategoriaProducto $categoriaProducto)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
        ]);

        DB::transaction(function () use ($datos, $categoriaProducto) {
            $nuevoNombre = trim($datos['nombre']);
            $slugBase = Str::slug($nuevoNombre, '_');
            $nuevoSlug = $slugBase !== '' ? $slugBase : 'categoria';
            $i = 1;
            while (
                CategoriaProducto::query()
                    ->where('slug', $nuevoSlug)
                    ->where('id', '!=', $categoriaProducto->id)
                    ->exists()
            ) {
                $i++;
                $nuevoSlug = $slugBase.'_'.$i;
            }

            if ($categoriaProducto->slug !== $nuevoSlug) {
                Producto::query()
                    ->where('categoria', $categoriaProducto->slug)
                    ->update(['categoria' => $nuevoSlug]);
            }

            $categoriaProducto->update([
                'nombre' => $nuevoNombre,
                'slug' => $nuevoSlug,
            ]);
        });

        return response()->json(['ok' => true, 'message' => 'Categoria actualizada.']);
    }

    public function categoriaToggle(CategoriaProducto $categoriaProducto)
    {
        $categoriaProducto->update([
            'activo' => ! $categoriaProducto->activo,
        ]);

        return response()->json([
            'ok' => true,
            'message' => $categoriaProducto->activo ? 'Categoria activada.' : 'Categoria inactivada.',
        ]);
    }

    private function guardarImagenes(Request $request, Producto $producto): void
    {
        if (! $request->hasFile('imagenes')) {
            return;
        }

        foreach ($request->file('imagenes') as $archivo) {
            if (! $archivo->isValid()) {
                continue;
            }

            $ruta = $archivo->store('productos', 'public');

            $producto->imagenes()->create([
                'archivo_path' => $ruta,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime_type' => $archivo->getClientMimeType(),
                'tamano_bytes' => $archivo->getSize(),
            ]);
        }
    }

    private function resolverRutaImagen(?string $rutaOriginal): ?string
    {
        $rutaOriginal = trim((string) $rutaOriginal);
        if ($rutaOriginal === '') {
            return null;
        }

        $candidatas = [];
        $candidatas[] = str_replace('\\', '/', $rutaOriginal);

        if (filter_var($rutaOriginal, FILTER_VALIDATE_URL)) {
            $pathUrl = parse_url($rutaOriginal, PHP_URL_PATH);
            if (is_string($pathUrl) && $pathUrl !== '') {
                $candidatas[] = str_replace('\\', '/', $pathUrl);
            }
        }

        foreach ($candidatas as $ruta) {
            $ruta = ltrim($ruta, '/');
            if ($ruta === '') {
                continue;
            }

            foreach ([
                $ruta,
                preg_replace('/^public\//', '', $ruta),
                preg_replace('/^storage\//', '', $ruta),
                preg_replace('/^app\/public\//', '', $ruta),
            ] as $opcion) {
                if (! is_string($opcion) || $opcion === '') {
                    continue;
                }
                if (Storage::disk('public')->exists($opcion)) {
                    return $opcion;
                }
            }
        }

        // Fallback legacy: si solo quedo guardado nombre de archivo, buscarlo en carpetas comunes.
        $nombreArchivo = basename(str_replace('\\', '/', $rutaOriginal));
        if ($nombreArchivo !== '' && $nombreArchivo !== '.' && $nombreArchivo !== '..') {
            $basePublic = Storage::disk('public')->path('');
            foreach (['productos', 'imagenes', 'uploads'] as $carpeta) {
                $coincidencias = File::glob($basePublic . DIRECTORY_SEPARATOR . $carpeta . DIRECTORY_SEPARATOR . $nombreArchivo);
                if (! empty($coincidencias)) {
                    $rutaReal = str_replace('\\', '/', $coincidencias[0]);
                    $baseNorm = str_replace('\\', '/', rtrim($basePublic, '\\/'));
                    $relativa = ltrim(Str::after($rutaReal, $baseNorm), '/');
                    if ($relativa !== '' && Storage::disk('public')->exists($relativa)) {
                        return $relativa;
                    }
                }
            }
        }

        return null;
    }
}
