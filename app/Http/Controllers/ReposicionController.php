<?php
namespace App\Http\Controllers;

use App\Models\Reposicion;
use App\Models\ReposicionDetalle;
use Illuminate\Http\Request;

class ReposicionController extends Controller
{
    public function index()
    {
        return Reposicion::with('atm', 'detalles')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'monto_repuesto' => 'required|numeric',
            'atm_id' => 'required|exists:atms,id',
            'detalles' => 'required|array',
            'detalles.*.cantidad' => 'required|integer',
            'detalles.*.denominacion' => 'required|numeric',
            'detalles.*.subtotal' => 'required|numeric',
        ]);

        $reposicion = Reposicion::create($request->only(['fecha', 'monto_repuesto', 'atm_id']));

        foreach ($request->detalles as $detalle) {
            $detalle['reposicion_id'] = $reposicion->id;
            ReposicionDetalle::create($detalle);
        }

        return response()->json($reposicion->load('detalles'), 201);
    }
}
