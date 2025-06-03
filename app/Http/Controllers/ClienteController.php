<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    // Obtener todos los clientes con sus usuarios relacionados
    public function index()
    {
        $clientes = Cliente::with('usuario')->get();
        return response()->json($clientes);
    }

    public function store(Request $request)
{
    // Validación
    $request->validate([
        'ci' => 'required|unique:clientes',
        'telefono' => 'required|integer',
        'direccion' => 'required|string',

    ]);

    // Generar contraseña si no se proporciona
    $password = $request->filled('password') 
        ? Hash::make($request->password) 
        : Hash::make($request->ci);

    // Buscar el rol "Cliente"
    $rolCliente = Rol::where('nombre', 'Cliente')->first();
    if (!$rolCliente) {
        return response()->json(['error' => 'El rol Cliente no está registrado.'], 400);
    }

    // Crear usuario
    $usuario = Usuario::create([
        'email' => $request->email,
        'password' => $password,
        'nombre_user' => $request->nombre_user,
        'nombre' => $request->nombre,
        'apellido' => $request->apellido,
        'genero' => $request->genero,
        'fecha_nacimiento' => $request->fecha_nacimiento,
    ]);

    // Asignar rol al usuario
    $usuario->roles()->attach($rolCliente->id);

    // Crear cliente vinculado al usuario
    $cliente = Cliente::create([
        'ci' => $request->ci,
        'direccion' => $request->direccion,
        'telefono' => $request->telefono,
        'usuario_id' => $usuario->id,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Cliente creado satisfactoriamente',
        'cliente' => $cliente->load('usuario'),
    ], 201);
}

    public function show(string $id)
    {
        $cliente = Cliente::with('usuario')->find($id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }


    public function update(Request $request, $id)
    {
        Log::info("Iniciando actualización del cliente ID: $id");
        Log::debug('Datos recibidos:', $request->all());
    
        try {
            // Buscar cliente
            $cliente = Cliente::with('usuario')->find($id);
            if (!$cliente) {
                Log::warning("Cliente no encontrado ID: $id");
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }
    
            Log::debug('Cliente encontrado:', ['id' => $cliente->id, 'ci' => $cliente->ci]);
    
            // Validar datos
            $validated = $request->validate([
                'ci' => "required|string|unique:clientes,ci,$id",
                'telefono' => 'required|integer',
                'direccion' => 'required|string',
                'nombre' => 'required|string|min:2|max:100',
                'apellido' => 'required|string|min:2|max:100',
                'email' => "required|email|unique:usuarios,email,".$cliente->usuario->id,
                'nombre_user' => 'required|string|min:2|max:100',
                'genero' => 'required|string|max:10',
                'fecha_nacimiento' => 'required|date',
                'password' => 'nullable|string|min:6',
            ]);
    
            Log::info('Datos validados correctamente');
    
            // Actualizar cliente
            $cliente->update([
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
                'direccion' => $validated['direccion'],
            ]);
    
            Log::debug('Cliente actualizado:', $cliente->toArray());
    
            // Preparar datos de usuario
            $usuarioData = [
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
                'nombre_user' => $validated['nombre_user'],
                'genero' => $validated['genero'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
            ];
    
            // Manejo de contraseña
            if (!empty($validated['password'])) {
                $usuarioData['password'] = Hash::make($validated['password']);
                Log::debug('Contraseña actualizada');
            } else {
                Log::debug('No se cambió la contraseña');
            }
    
            // Actualizar usuario relacionado
            if ($cliente->usuario) {
                $cliente->usuario->update($usuarioData);
                Log::debug('Usuario actualizado:', $cliente->usuario->toArray());
            } else {
                Log::error('El cliente no tiene usuario asociado', ['cliente_id' => $cliente->id]);
                return response()->json(['error' => 'El cliente no tiene usuario asociado'], 400);
            }
    
            // Cargar relaciones actualizadas
            $cliente->load('usuario');
    
            Log::info("Actualización exitosa para cliente ID: $id");
            
            return response()->json([
                'status' => true,
                'message' => 'Cliente y usuario actualizados correctamente.',
                'cliente' => $cliente
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', ['errors' => $e->errors()]);
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error en actualización:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Error interno al actualizar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'status' => false,
                    'error' => 'Cliente no encontrado'
                ], 404);
            }

            // Alternar el estado (toggle) entre 0 y 1
            $cliente->estado = $cliente->estado == 1 ? 0 : 1;
            $cliente->save();

            return response()->json([
                'status' => true,
                'message' => $cliente->estado == 1 
                    ? 'Cliente reactivado correctamente' 
                    : 'Cliente eliminado correctamente',
                'cliente' => $cliente,
                'nuevo_estado' => $cliente->estado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al cambiar el estado del cliente',
                'details' => $e->getMessage()
            ], 500);
        }
    }
/*
    public function destroy(string $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['status' => false, 'error' => 'Cliente no encontrado'], 404);
        }

        $usuario = $cliente->usuario;

        $cliente->delete();
        if ($usuario) {
            $usuario->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Cliente y usuario eliminado satisfactoriamente'
        ]);
    }
        */
}
