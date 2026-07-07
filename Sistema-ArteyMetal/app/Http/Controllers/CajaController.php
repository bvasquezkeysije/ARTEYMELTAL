<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function index()
    {
        $aperturas = CajaApertura::query()
            ->with('usuario')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('cajas.index', compact('aperturas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255',
        ]);

        CajaApertura::create([
            'usuario_id' => Auth::id(),
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

        $totalVentas = $cajaApertura->ventas()->sum('monto_total');

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

        return view('cajas.show', compact('cajaApertura', 'ventas'));
    }
}
