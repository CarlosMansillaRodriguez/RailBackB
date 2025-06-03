<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;

class ReporteController extends Controller
{
    public function index()
    {
        //return Reporte::all();
        $reporte = Reporte::with('usuario')->get();
        return response()->json($reporte);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {    
        $request->validate([
            'tipo' => 'required|string',
            'formato_de1_exportacion' => 'required|string',
            'fecha_de_inicio' => 'required|date',
            'fecha_de_final' => 'required|date',
            'filtro' => 'required|string',
            'usuario_id' => 'nullable|exists:usuarios,id',
        ]);

        $reporte = Reporte::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Reporte creado satisfactoriamente',
            'reporte' => $reporte
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Reporte::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->update($request->all());
        return response()->json($reporte, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Reporte::destroy($id);
        return response()->json(null, 204);
    }
}
