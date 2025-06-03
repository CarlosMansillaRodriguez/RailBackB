<?php
//Achivo creado por Carlos
namespace App\Http\Controllers;


use App\Models\Usuario;
//use Auth;
use Illuminate\Http\Request;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\Auth;

class TarjetaController extends Controller
{
    private function autorizar()
    {
        $user = Auth::Usuario();
        if (!$user || (!$user->hasRol('Administrador') && !$user->hasRol('cliente'))) {
            abort(403, 'No autorizado');
        }
    }
    public function index()
    {
        
        // Obtener todas las tarjetas con sus cuentas y clientes asociados
        $tarjetas = Tarjeta::with('cuenta.cliente.usuario')->get();

        return response()->json([
            'status' => true,
            'message' => 'Lista de tarjetas obtenida correctamente.',
            'tarjetas' => $tarjetas
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
            'numero' => 'required|string',
            'tipo' => 'required|string',
            'cvc' => 'required|string',
            'fecha_vencimiento' => 'required|date',
            'estado' => 'nullable|string',
            'cuenta_id' => 'required|exists:cuentas,id',
        ]);
         // Asignar estado por defecto si no viene
        if (!isset($validatedData['estado'])) {
            $validatedData['estado'] = 'activa'; // o true
        }
        $tarjeta = Tarjeta::create($request->all());

        if (!$tarjeta) {
            return response()->json([
                'status' => false,
                'error' => 'No se pudo crear la tarjeta',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Tarjeta creada satisfactoriamente',
            'tarjeta' => $tarjeta
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
        $tarjeta = Tarjeta::with('cuenta')->findOrFail($id);
        return response()->json($tarjeta);
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
        $tarjeta = Tarjeta::findOrFail($id);
        $tarjeta->update($request->all());
        return response()->json($tarjeta);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //$this->autorizar();
        /* $tarjeta = Tarjeta::findOrFail($id);
        $tarjeta->delete();
        return response()->json(['message' => 'Tarjeta eliminada']); */
        
            try {
                // Buscar la tarjeta por ID
                $tarjeta = Tarjeta::find($id);
        
                if (!$tarjeta) {
                    return response()->json([
                        'status' => false,
                        'error' => 'Tarjeta no encontrada'
                    ], 404);
                }
        
                // Alternar el estado entre "Activado" y "Desactivado"
                $nuevoEstado = $tarjeta->estado === 'Activado' ? 'Desactivado' : 'Activado';
        
                // Actualizar y guardar
                $tarjeta->estado = $nuevoEstado;
                $tarjeta->save();
        
                return response()->json([
                    'status' => true,
                    'message' => $nuevoEstado === 'Activado' 
                        ? 'Tarjeta reactivada correctamente' 
                        : 'Tarjeta desactivada correctamente',
                    'tarjeta' => $tarjeta,
                    'nuevo_estado' => $nuevoEstado
                ]);
        
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error al cambiar el estado de la tarjeta',
                    'details' => $e->getMessage()
                ], 500);
            }
    }
}
