<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaApertura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function index()
    {
        $busqueda = request('q');
        $filtroEstado = request('estado');
        $busquedaCaja = request('cq');
        $filtroCajaEstado = request('cq_estado');

        $cajas = Caja::with(['aperturas' => function ($query) {
            $query->latest()->limit(2);
        }])->when($busquedaCaja, function ($query) use ($busquedaCaja) {
            $query->where('nombre', 'like', "%{$busquedaCaja}%");
        })->get()->map(function ($caja) {
            $ultimaAbierta = $caja->aperturas->firstWhere('estado', 'abierta');
            $ultimoCierre = $caja->aperturas->firstWhere('estado', 'cerrada');
            $caja->ultima_apertura = $ultimaAbierta;
            $caja->ultimo_monto_final = $ultimoCierre?->monto_final;
            return $caja;
        });

        if ($filtroCajaEstado !== null && $filtroCajaEstado !== '') {
            $cajas = $cajas->filter(function ($caja) use ($filtroCajaEstado) {
                $ultima = $caja->aperturas->first();
                if ($filtroCajaEstado === 'abierta') {
                    return $ultima && $ultima->estado === 'abierta';
                }
                return !$ultima || $ultima->estado !== 'abierta';
            })->values();
        }

        $aperturas = CajaApertura::query()
            ->with('usuario', 'caja')
            ->withCount('ventas')
            ->withSum('ventas', 'monto_total')
            ->withSum('ventas', 'monto_efectivo')
            ->withSum('ventas', 'monto_digital')
            ->withSum('ventas', 'vuelto')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhereHas('usuario', function ($uq) use ($busqueda) {
                            $uq->where('name', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->when($filtroEstado !== null && $filtroEstado !== '', function ($query) use ($filtroEstado) {
                $query->where('estado', $filtroEstado);
            })
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('cajas.index', compact('cajas', 'aperturas', 'busqueda', 'filtroEstado', 'busquedaCaja', 'filtroCajaEstado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:cajas,id',
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $caja = Caja::findOrFail($request->caja_id);

        $existeAbierta = CajaApertura::where('caja_id', $caja->id)
            ->where('estado', 'abierta')
            ->exists();

        if ($existeAbierta) {
            return back()->withErrors(['caja_id' => 'Esta caja ya está abierta.']);
        }

        CajaApertura::create([
            'usuario_id' => Auth::id(),
            'caja_id' => $caja->id,
            'nombre' => $caja->nombre,
            'monto_inicial' => $request->monto_inicial,
            'observaciones' => $request->observaciones,
            'estado' => 'abierta',
        ]);

        return redirect()->route('cajas.index')->with('success', 'Caja abierta correctamente.');
    }

    public function cerrar(Request $request, CajaApertura $cajaApertura)
    {
        if ($cajaApertura->estado !== 'abierta') {
            return back()->withErrors(['caja' => 'Esta caja ya está cerrada.']);
        }

        $request->validate([
            'monto_final' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $totalVentas = $cajaApertura->ventas()->sum("monto_efectivo") + $cajaApertura->ventas()->sum("monto_digital") - $cajaApertura->ventas()->sum("vuelto");

        $cajaApertura->update([
            'fecha_cierre' => now(),
            'monto_final' => $request->monto_final,
            'total_ventas' => $totalVentas,
            'estado' => 'cerrada',
            'observaciones' => $request->observaciones ?: $cajaApertura->observaciones,
        ]);

        if (session('caja_apertura_id') == $cajaApertura->id) {
            session()->forget('caja_apertura_id');
        }

        return redirect()->route('cajas.index')->with('success', 'Caja cerrada correctamente.');
    }

    public function show(CajaApertura $cajaApertura)
    {
        $ventas = $cajaApertura->ventas()->orderBy('created_at', 'desc')->get();

        $totalEfectivoVentas = $cajaApertura->ventas()->sum('monto_efectivo');
        $totalDigitalVentas = $cajaApertura->ventas()->sum('monto_digital');
        $totalVuelto = $cajaApertura->ventas()->sum('vuelto');
        $cantidadVentas = $cajaApertura->ventas()->count();

        return view('cajas.show', compact(
            'cajaApertura',
            'ventas',
            'totalEfectivoVentas',
            'totalDigitalVentas',
            'totalVuelto',
            'cantidadVentas',
        ));
    }

    public function update(Request $request, Caja $caja)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:cajas,nombre,' . $caja->id,
            'activa' => 'required|boolean',
        ]);

        $caja->update($request->only('nombre', 'activa'));

        return redirect()->route('cajas.index')->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(Caja $caja)
    {
        $tieneAperturas = $caja->aperturas()->exists();

        if ($tieneAperturas) {
            return back()->withErrors(['caja' => 'No se puede eliminar una caja que tiene registros de apertura.']);
        }

        $caja->delete();

        return redirect()->route('cajas.index')->with('success', 'Caja eliminada correctamente.');
    }
}
