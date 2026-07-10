<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function panelData()
    {
        $roles = Rol::query()
            ->withCount(['usuarios', 'permisos'])
            ->with('permisos:id,nombre')
            ->orderBy('nombre')
            ->get();

        $permisos = Permiso::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json([
            'ok' => true,
            'roles' => $roles,
            'permisos' => $permisos,
        ]);
    }

    public function index()
    {
        $busqueda = request('q');

        $roles = Rol::query()
            ->withCount(['usuarios', 'permisos'])
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('descripcion', 'like', "%{$busqueda}%");
            })
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('roles.index', compact('roles', 'busqueda'));
    }

    public function create()
    {
        $permisos = Permiso::query()->orderBy('nombre')->get();

        return view('roles.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $rol = Rol::create([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'activo' => $request->boolean('activo', true),
        ]);

        $rol->permisos()->sync($datos['permisos'] ?? []);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Rol registrado correctamente.']);
        }

        return redirect()->route('roles.index')->with('ok', 'Rol registrado correctamente.');
    }

    public function edit(Rol $role)
    {
        $permisos = Permiso::query()->orderBy('nombre')->get();
        $role->load('permisos:id');

        return view('roles.edit', ['rol' => $role, 'permisos' => $permisos]);
    }

    public function update(Request $request, Rol $role)
    {
        $datos = $this->validar($request, $role->id);

        $role->update([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'activo' => $request->boolean('activo', true),
        ]);

        $role->permisos()->sync($datos['permisos'] ?? []);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Rol actualizado correctamente.']);
        }

        return redirect()->route('roles.index')->with('ok', 'Rol actualizado correctamente.');
    }

    public function destroy(Rol $role)
    {
        if ($role->usuarios()->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se puede eliminar: el rol tiene usuarios asignados.',
                ], 422);
            }

            return redirect()->route('roles.index')->with('ok', 'No se puede eliminar: el rol tiene usuarios asignados.');
        }

        $role->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Rol eliminado correctamente.']);
        }

        return redirect()->route('roles.index')->with('ok', 'Rol eliminado correctamente.');
    }

    private function validar(Request $request, ?int $rolId = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:80', Rule::unique('roles', 'nombre')->ignore($rolId)],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'activo' => ['nullable', 'boolean'],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['integer', 'exists:permisos,id'],
        ]);
    }
}
