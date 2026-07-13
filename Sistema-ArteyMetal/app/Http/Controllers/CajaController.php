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
        $aperturas = CajaApertura::query()
            ->with('usuario', 'caja')
            ->withCount('ventas')
            ->withSum('ventas', 'monto_total')
            ->withSum('ventas', 'monto_efectivo')
            ->withSum('ventas', 'monto_digital')
            ->withSum('ventas', 'vuelto')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $cajasDisponibles = Caja::where('activa', true)->get();

        return view('cajas.index', compact('aperturas', 'cajasDisponibles'));
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
}
