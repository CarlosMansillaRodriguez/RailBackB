<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use Illuminate\Http\Request;

class IncidenciaController extends Controller
{
    public function index()
    {
        return Incidencia::with('cliente', 'tecnico')->get();
    }

    public function store(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

    try {
        $validated = $request->validate([
            'descripcion' => 'required|string',
            'estado' => 'required|string',
            'fecha_reporte' => 'required|date',
            'fecha_solucion' => 'nullable|date',
            'tipo' => 'required|string',
            'cliente_id' => 'required|exists:clientes,id',
            'tecnico_id' => 'required|exists:tecnicos,id',
        ]);

        $incidencia = Incidencia::create($validated);

        return response()->json([
            'message' => 'Incidencia registrada con éxito',
            'data' => $incidencia
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al crear la incidencia',
            'details' => $e->getMessage()
        ], 500);
    }
    }

    public function show($id)
    {
        $incidencia = Incidencia::with('cliente', 'tecnico')->findOrFail($id);
        return response()->json($incidencia);
    }

    public function update(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);

        $validated = $request->validate([
            'descripcion' => 'sometimes|string',
            'estado' => 'sometimes|string',
            'fecha_reporte' => 'sometimes|date',
            'fecha_solucion' => 'nullable|date',
            'tipo' => 'sometimes|string',
            'cliente_id' => 'sometimes|exists:clientes,id',
            'tecnico_id' => 'sometimes|exists:tecnicos,id',
        ]);

        $incidencia->update($validated);

        return response()->json(['message' => 'Incidencia actualizada', 'data' => $incidencia]);
    }

    public function destroy($id)
    {
        Incidencia::findOrFail($id)->delete();
        return response()->json(['message' => 'Incidencia eliminada']);
    }
}
