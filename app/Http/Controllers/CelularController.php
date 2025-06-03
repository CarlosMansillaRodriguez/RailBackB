<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Celular;

class CelularController extends Controller
{
    // Registro de celular SIN token (solo email y contraseña)
    public function registrarConCredenciales(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'ip' => 'required|ip',
            'modelo' => 'required|string',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $roles = $usuario->roles->pluck('nombre')->map(fn($r) => strtolower($r))->toArray();
        if (!in_array('cliente', $roles)) {
            return response()->json(['error' => 'Solo clientes pueden registrar celular'], 403);
        }

        $celular = Celular::create([
            'ip' => $request->ip,
            'modelo' => $request->modelo,
        ]);

        $cuenta = $usuario->cliente->cuenta ?? null;
        if ($cuenta) {
            $cuenta->celular_id = $celular->id;
            $cuenta->save();
        }

        return response()->json([
            'mensaje' => 'Celular registrado correctamente',
            'celular' => $celular
        ], 201);
    }
}
