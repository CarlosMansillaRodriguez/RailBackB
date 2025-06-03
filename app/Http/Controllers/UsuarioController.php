<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    // Obtener todos los usuarios con sus roles
    public function index()
    {
        $usuarios = Usuario::with('roles')->get();
        return response()->json($usuarios);
    }

    // Crear un nuevo usuario y asignar roles
    public function store(Request $request)
    {
    
        try {
            // 🛠️ Validación de datos
            $validated = $request->validate([
                'nombre_user' => 'required|string|max:100',
                'email' => 'required|email|unique:usuarios,email',
                'password' => 'required|string|min:6',
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'genero' => 'required|string|max:1',
                'fecha_nacimiento' => 'required|date',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:roles,id'
            ]);
    
            // 🧱 Crear usuario
            $usuario = Usuario::create([
                'nombre_user' => $request->nombre_user,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'genero' => $request->genero,
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ]);
    
            // 🔐 Asignar roles al usuario
            /* if ($request->has('roles')) {
                $usuario->roles()->attach($request->roles);
                Log::info("Roles asignados", ['roles' => $request->roles]);
            } */
            // Asignar roles al usuario
            $usuario->roles()->attach($request->roles);

            // ✅ Respuesta de éxito
            return response()->json([
                'status' => true,
                'message' => 'Usuario creado correctamente',
                'usuario' => $usuario->load('roles'),
            ],);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // ❗ Errores de validación (422)
            Log::warning("Errores de validación", [
                'errores' => $e->errors(),
                'mensaje' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false,
                'error' => 'Datos inválidos',
                'errors' => $e->errors()
            ], );
        } catch (\Exception $e) {
            // 💥 Excepciones generales o errores internos (500)
            Log::error("Error interno al crear usuario", [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'status' => false,
                'error' => 'Ocurrió un error al crear el usuario',
                'detalle' => $e->getMessage()
            ],);
        }
    }

    // Mostrar un usuario específico con roles
    public function show($id)
    {
        $usuario = Usuario::with(['roles'/* , 'bitacoras' */])->find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    // Actualizar datos y roles de un usuario
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'nombre_user' => 'sometimes|required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'password' => 'nullable|string|min:6',
            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'genero' => 'sometimes|required|string|max:1',
            'fecha_nacimiento' => 'sometimes|required|date',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);
        
        // Actualizar los campos del usuario
        if ($request->has('nombre_user')) $usuario->nombre_user = $request->nombre_user;
        if ($request->has('email')) $usuario->email = $request->email;
        if ($request->filled('password')) $usuario->password = Hash::make($request->password);
        if ($request->has('nombre')) $usuario->nombre = $request->nombre;
        if ($request->has('apellido')) $usuario->apellido = $request->apellido;
        if ($request->has('genero')) $usuario->genero = $request->genero;
        if ($request->has('fecha_nacimiento')) $usuario->fecha_nacimiento = $request->fecha_nacimiento;
        $usuario->save();
        
        // Si se enviaron nuevos roles, sincronizarlos
        if ($request->has('roles')) {
            $usuario->roles()->sync($request->roles);
        }
        Log::info($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $usuario->load('roles')
        ]);
    }

    // Eliminar un usuario y sus relaciones con roles
    public function destroy($id)
    {
        try {
            $usuario = Usuario::find($id);
    
            if (!$usuario) {
                return response()->json([
                    'status' => false,
                    'error' => 'Usuario no encontrado'
                ], 404);
            }
    
            // Verificar si el usuario intenta desactivarse a sí mismo
            $userAuth = Auth::guard('sanctum')->user();

            if ($userAuth && $userAuth->id == $usuario->id) {
                return response()->json([
                    'status' => false,
                    'error' => 'No puedes desactivar tu propia cuenta'
                ], 403);
            }
    
            // Alternar el estado (toggle) entre 0 (inactivo) y 1 (activo)
            $usuario->estado = $usuario->estado == 1 ? 0 : 1;
            $usuario->save();
    
            // Opcional: Registrar en bitácora
            /* if (class_exists('App\Models\Bitacora')) {
                Bitacora::create([
                    'usuario_id' => auth()->id(),
                    'accion' => $usuario->estado == 1 
                        ? 'Reactivación de usuario' 
                        : 'Desactivación de usuario',
                    'detalles' => "Usuario ID: {$id}, Estado nuevo: {$usuario->estado}"
                ]);
            } */
    
            return response()->json([
                'status' => true,
                'message' => $usuario->estado == 1 
                    ? 'Usuario reactivado correctamente' 
                    : 'Usuario desactivado correctamente',
                'usuario' => $usuario,
                'nuevo_estado' => $usuario->estado
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al cambiar el estado del usuario',
                'details' => $e->getMessage()
            ], 500);
        }
        /* $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Eliminar relaciones en la tabla pivote
        $usuario->roles()->detach();

        // Eliminar usuario
        $usuario->delete();

        return response()->json([
            'status' => true,
            'message' => 'Usuario eliminado correctamente'
        ]); */
    }

    // Eliminar un usuario y sus relaciones con roles
    /*
    public function destroy($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Eliminar relaciones en la tabla pivote
        $usuario->roles()->detach();

        // Eliminar usuario
        $usuario->delete();

        return response()->json([
            'status' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
    */

    public function asignarRoles(Request $request, $id)
    {
        $usuario = Usuario::find($id);

    if (!$usuario) {
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }

    $request->validate([
        'roles' => 'required|array',
        'roles.*' => 'exists:roles,id'
    ]);

    $usuario->roles()->syncWithoutDetaching($request->roles);

    return response()->json([
        'message' => 'Roles asignados correctamente',
        'usuario' => $usuario->load('roles')
    ]);
}

// Revocar uno o más roles de un usuario
public function revocarRoles(Request $request, $id)
{
    $usuario = Usuario::find($id);

    if (!$usuario) {
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }

    $request->validate([
        'roles' => 'required|array',
        'roles.*' => 'exists:roles,id'
    ]);

    // Elimina solo los roles indicados
    $usuario->roles()->detach($request->roles);

    return response()->json([
        'message' => 'Roles revocados correctamente',
        'usuario' => $usuario->load('roles')
    ]);
}

}
