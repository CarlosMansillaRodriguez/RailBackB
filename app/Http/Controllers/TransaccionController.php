<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaccion;
use Illuminate\Support\Facades\DB;


class TransaccionController extends Controller
{
    
    /**
     * Mostrar todas las transacciones.
     */
    public function index()
    {
        $transacciones = Transaccion::with(['cuentaOrigen.cliente.usuario', 'cuentaDestino.cliente.usuario'])->get();

        return response()->json([
            'success' => true,
            'transacciones' => $transacciones,
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
    // Validaciones y preparación de datos
    $validated = $request->validate([
        'tipo_transaccion' => 'required|in:Depósito,Retiro,Transferencia',
        'monto' => 'required|numeric|min:0.01',
        'descripcion' => 'nullable|string|max:500',
        'id_cuenta_origen' => 'nullable|exists:cuentas,id',
        'id_cuenta_destino' => 'nullable|exists:cuentas,id',
    ]);

    if ($validated['tipo_transaccion'] === 'Depósito') {
        $validated['id_cuenta_origen'] = null;
    } elseif ($validated['tipo_transaccion'] === 'Retiro') {
        $validated['id_cuenta_destino'] = null;
    }

    try {
        $resultado = DB::select("SELECT * FROM procesar_transaccion(?, ?, ?, ?, ?)", [
            $validated['tipo_transaccion'],
            $validated['monto'],
            $validated['id_cuenta_origen'],
            $validated['id_cuenta_destino'],
            $validated['descripcion'] ?? ''
        ]);

        if (!empty($resultado) && isset($resultado[0]->resultado)) {
            if ($resultado[0]->resultado === 'Transacción completada') {
                // Buscar la transacción recién insertada por coincidencia de datos
                $transaccion = Transaccion::where([
                        ['monto', '=', $validated['monto']],
                        ['tipo_transaccion', '=', $validated['tipo_transaccion']],
                        ['descripcion', '=', $validated['descripcion'] ?? ''],
                        ['id_cuenta_origen', '=', $validated['id_cuenta_origen']],
                        ['id_cuenta_destino', '=', $validated['id_cuenta_destino']],
                    ])
                    ->latest('id') // asegura que traes la última que coincide
                    ->first();

                return response()->json([
                    'success' => true,
                    'message' => 'Transacción procesada correctamente.',
                    'transaccion' => $transaccion,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $resultado[0]->resultado,
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se recibió un mensaje esperado desde el SP.',
            ], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la transacción: ' . $e->getMessage(),
        ], 500);
    }
}


    /**
     * Mostrar una transacción específica por ID.
     */
    public function show(string $id)
    {
        $transaccion = Transaccion::with(['cuentaOrigen', 'cuentaDestino'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'transaccion' => $transaccion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualizar una transacción específica.
     */
    public function update(Request $request, string $id)
    {
        $transaccion = Transaccion::findOrFail($id);

        $validated = $request->validate([
            'estado_transaccion' => 'nullable|string|in:pendiente,completada,rechazada,anulada',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $transaccion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transacción actualizada correctamente.',
            'transaccion' => $transaccion,
        ]);
    }

    /**
     * Eliminar una transacción específica.
     */
    public function destroy(string $id)
    {
        $transaccion = Transaccion::findOrFail($id);
        $transaccion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transacción eliminada correctamente.',
        ]);
    }


}
