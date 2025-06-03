<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Heredero;

class HerederoController extends Controller
{
  /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los herederos con sus cuentas
        $heredero = Heredero::with('cuenta.heredero')->get();

        return response()->json([
            'status' => true,
            'message' => 'Lista de herederos obtenida correctamente.',
            'herederos' => $heredero
        ]);
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
        try {
            // Validación de datos
            $request->validate([
                'ci' => 'required|string',
                'nombre_completo' => 'required|string',
                'parentesco' => 'required|string',
                'fecha_registro' => 'required|date',
                'telefono' => 'nullable|string',
                'monto' => 'required|integer',
                'cuenta_id' => 'required|exists:cuentas,id',
            ]);

             // Asignar estado por defecto si no viene
            if (!isset($validatedData['estado'])) {
                $validatedData['estado'] = 'activa'; // o true
            }
            $heredero = Heredero::create($request->all());

            if (!$heredero) {
                return response()->json([
                    'status' => false,
                    'error' => 'No se pudo crear el heredero',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Heredero creada satisfactoriamente',
                'heredero' => $heredero
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //$this->autorizar();
        $heredero = Heredero::with('cuenta')->findOrFail($id);
        return response()->json($heredero);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //$this->autorizar();
        $heredero = Heredero::findOrFail($id);
        $heredero->update($request->all());
        return response()->json($heredero);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
        $heredero = Heredero::find($id);

        if (!$heredero) {
            return response()->json([
                'status' => false,
                'error' => 'Heredero no encontrado'
            ], 404);
        }

        // Alternar el estado entre true y false
        $nuevoEstado = !$heredero->estado;

        $heredero->estado = $nuevoEstado;
        $heredero->save();

        return response()->json([
            'status' => true,
            'message' => $nuevoEstado ? 'Heredero activado correctamente' : 'Heredero desactivado correctamente',
            'heredero' => $heredero,
            'nuevo_estado' => $nuevoEstado ? 'Activado' : 'Desactivado'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Error al cambiar el estado del heredero',
            'details' => $e->getMessage()
        ], 500);
    }
    }  
}
