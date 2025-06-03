<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    // Obtener todos los empleados con sus usuarios relacionados
    public function index()
    {
        $empleados = Empleado::with('usuario')->get();
        return response()->json($empleados);
    }

    public function store(Request $request)
    {
        // Solo administradores pueden registrar empleados
        // if (!$request->user()->hasRol('Administrador')) {
        //     return response()->json(['error' => 'Acceso denegado'], 403);
        // }

        // Validación
        $request->validate([
            'cargo' => 'required|string|max:100',
            'fecha_contrato' => 'required|date',
            'horario_entrada' => 'required|date_format:H:i',
            'horario_salida' => 'required|date_format:H:i',
        ]);

        // Generar contraseña
        $password = Hash::make($request->password);

        // Buscar el rol "Empleado"
        $rolEmpleado = Rol::where('nombre', 'Empleado')->first();
        if (!$rolEmpleado) {
            return response()->json(['error' => 'El rol Empleado no está registrado.'], 400);
        }

        // Crear usuario
        $usuario = Usuario::create(
            [
                'email' => $request->email,
                'password' => $password,
                'nombre_user' => $request->nombre_user,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'genero' => $request->genero,
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ]
        );

        // Asignar rol al usuario
        $usuario->roles()->attach($rolEmpleado->id);

        // Crear empleado vinculado al usuario
        $empleado = Empleado::create([
            'cargo' => $request->cargo,
            'fecha_contrato' => $request->fecha_contrato,
            'horario_entrada' => $request->horario_entrada,
            'horario_salida' => $request->horario_salida,
            'usuario_id' => $usuario->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Empleado creado correctamente',
            'empleado' => $empleado->load('usuario')
        ], 201);
    }

    public function show(string $id)
    {
        $empleado = Empleado::with('usuario')->find($id);

        if (!$empleado) {
            return response()->json(['error' => 'Empleado no encontrado'], 404);
        }

        return response()->json($empleado);
    }

    public function update(Request $request, $id)
    {
        Log::info("Iniciando actualización del empleado ID: $id");
        Log::debug('Datos recibidos:', $request->all());

        try {
            // Buscar empleado
            $empleado = Empleado::with('usuario')->find($id);
            if (!$empleado) {
                Log::warning("Empleado no encontrado ID: $id");
                return response()->json(['error' => 'Empleado no encontrado'], 404);
            }

            Log::debug('Empleado encontrado:', ['id' => $empleado->id]);

            // Validar datos
            $validated = $request->validate([
                'cargo' => 'required|string|max:100',
                'fecha_contrato' => 'required|date',
                'horario_entrada' => 'required|date_format:H:i',
                'horario_salida' => 'required|date_format:H:i',
                'nombre' => 'required|string|min:2|max:100',
                'apellido' => 'required|string|min:2|max:100',
                'email' => "required|email|unique:usuarios,email,".$empleado->usuario->id,
                'nombre_user' => 'required|string|min:2|max:100',
                'genero' => 'required|string|max:10',
                'fecha_nacimiento' => 'required|date',
                'password' => 'nullable|string|min:6',
            ]);

            Log::info('Datos validados correctamente');

            // Actualizar cliente
            $empleado->update([
                'cargo' => $validated['cargo'],
                'horario_entrada' => $validated['horario_entrada'],
                'horario_salida' => $validated['horario_salida'],
                'fecha_contrato' => $validated['fecha_contrato'],
            ]);

            Log::debug('Empleado actualizado:', $empleado->toArray());

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
            if ($empleado->usuario) {
                $empleado->usuario->update($usuarioData);
                Log::debug('Usuario actualizado:', $empleado->usuario->toArray());
            } else {
                Log::error('El empleado no tiene usuario asociado', ['empleado_id' => $empleado->id]);
                return response()->json(['error' => 'El empleado no tiene usuario asociado'], 400);
            }

            // Cargar relaciones actualizadas
            $empleado->load('usuario');

            Log::info("Actualización exitosa para cliente ID: $id");

            return response()->json([
                'status' => true,
                'message' => 'Cliente y usuario actualizados correctamente.',
                'empleado' => $empleado
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
            $empleado = Empleado::find($id);

            if (!$empleado) {
                return response()->json([
                    'status' => false,
                    'error' => 'Empleado no encontrado'
                ], 404);
            }

            // Alternar el estado (toggle) entre 0 y 1
            $empleado->estado = $empleado->estado == 1 ? 0 : 1;
            $empleado->save();

            return response()->json([
                'status' => true,
                'message' => $empleado->estado == 1
                    ? 'Empleado reactivado correctamente'
                    : 'Empleado eliminado correctamente',
                'empleado' => $empleado,
                'nuevo_estado' => $empleado->estado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al cambiar el estado del empleado',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    /*
    public function destroy(string $id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json(['status' => false, 'error' => 'Empleado no encontrado'], 404);
        }

        $usuario = $empleado->usuario;

        $empleado->delete();
        if ($usuario) {
            $usuario->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Empleado y usuario eliminado satisfactoriamente'
        ]);
    }
        */
}
