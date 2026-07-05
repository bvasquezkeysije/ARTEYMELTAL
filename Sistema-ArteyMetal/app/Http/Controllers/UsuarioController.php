<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $busqueda = request('q');
        $filtroRol = request('rol_id');
        $filtroActivo = request('activo');

        $usuarios = User::query()
            ->with('rol')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('name', 'like', "%{$busqueda}%")
                    ->orWhere('email', 'like', "%{$busqueda}%");
            })
            ->when($filtroRol, function ($query) use ($filtroRol) {
                $query->where('rol_id', $filtroRol);
            })
            ->when($filtroActivo !== null && $filtroActivo !== '', function ($query) use ($filtroActivo) {
                $query->where('activo', $filtroActivo);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $roles = Rol::query()->where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'busqueda', 'filtroRol', 'filtroActivo', 'roles'));
    }

    public function create()
    {
        $roles = Rol::query()->where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol_id' => $datos['rol_id'],
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Rol::query()->where('activo', true)->orderBy('nombre')->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $this->validar($request, $usuario->id);

        $payload = [
            'name' => $datos['name'],
            'email' => $datos['email'],
            'rol_id' => $datos['rol_id'],
            'activo' => $request->boolean('activo', true),
        ];

        if (! empty($datos['password'])) {
            $payload['password'] = Hash::make($datos['password']);
        }

        $usuario->update($payload);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function toggleActivo(User $usuario)
    {
        if ((int) auth()->id() === (int) $usuario->id) {
            return redirect()->route('usuarios.index')->with('ok', 'No puedes desactivar tu propio usuario.');
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        return redirect()->route('usuarios.index')->with('ok', 'Estado del usuario actualizado.');
    }

    private function validar(Request $request, ?int $usuarioId = null): array
    {
        $reglasPassword = $usuarioId
            ? ['nullable', 'string', 'min:6', 'confirmed']
            : ['required', 'string', 'min:6', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuarioId)],
            'password' => $reglasPassword,
            'rol_id' => ['required', 'integer', 'exists:roles,id'],
            'activo' => ['nullable', 'boolean'],
        ]);
    }
}
