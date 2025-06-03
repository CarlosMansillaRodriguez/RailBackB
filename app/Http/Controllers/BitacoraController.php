<?php

namespace App\Http\Controllers;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(){
        // $bitacora = Bitacora::all();
        $bitacora = Bitacora::with('usuario')->get();
        return response()->json($bitacora);
    }

    public function store(Request $request)
    {
        $bitacora = Bitacora::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Bitacora creada satisfactoriamente',
            'bitacora' => $bitacora
        ], 201);
    }

    // public function registrarBitacora(Request $request, $descripcion)
    // {
    //     Bitacora::create([
    //         'usuario_id' => auth()->id(),
    //         'fecha' => now(),
    //         'ip_usuario' => Request::ip(),
    //         'descripcion' => $descripcion
    //     ]);
    // }
}
