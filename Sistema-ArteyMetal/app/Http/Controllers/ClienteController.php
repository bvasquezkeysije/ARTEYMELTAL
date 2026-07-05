<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $busqueda = request('q');
        $filtroDocumento = request('documento');

        $clientes = Cliente::query()
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre_completo', 'like', "%{$busqueda}%")
                    ->orWhere('telefono', 'like', "%{$busqueda}%")
                    ->orWhere('correo', 'like', "%{$busqueda}%")
                    ->orWhere('documento', 'like', "%{$busqueda}%");
            })
            ->when($filtroDocumento, function ($query) use ($filtroDocumento) {
                $query->where('documento', 'like', "%{$filtroDocumento}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'busqueda', 'filtroDocumento'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        Cliente::create($this->validarCliente($request));

        return redirect()->route('clientes.index')->with('ok', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validarCliente($request, $cliente->id));

        return redirect()->route('clientes.index')->with('ok', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('ok', 'Cliente eliminado correctamente.');
    }

    private function validarCliente(Request $request, ?int $clienteId = null): array
    {
        return $request->validate([
            'nombre_completo' => ['required', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'string', 'email', 'max:120'],
            'documento' => ['nullable', 'string', 'max:25', 'unique:clientes,documento'.($clienteId ? ',' . $clienteId : '')],
            'direccion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ]);
    }
}
