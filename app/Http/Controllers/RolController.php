<?php

namespace App\Http\Controllers;
use App\Models\Rol;
use App\Models\Permiso;

use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::with('permisos')->get();
        return response()->json($roles);
    }

    // Crear nuevo rol
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:roles,nombre',
            'descripcion' => 'nullable|string',
            'permisos' => 'sometimes|array', // Añade validación para permisos
            'permisos.*' => 'exists:permisos,id' // Valida que los permisos existan
            
        ]);

        //$rol = Rol::create($validated);
        $rol = Rol::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null
        ]);

        // Asignar permisos si existen en el request
        if (isset($validated['permisos'])) {
            $rol->permisos()->sync($validated['permisos']);
        }

        return response()->json([
            'message' => 'Rol creado correctamente',
            'message' => 'Rol creado satisfactoriamente',
            'rol' => $rol
        ], 201);
    }

        /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rol = Rol::find($id);

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json($rol);
    }

    // Actualizar un rol
/*     public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontró el rol'
            ], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|unique:roles,nombre,' . $id,
            'descripcion' => 'nullable|string',
        ]);

        $rol->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Rol actualizado correctamente',
            'rol' => $rol
        ], 200);
    } */

public function update(Request $request, $id)
{
    $rol = Rol::findOrFail($id);

    if (!$rol) {
        return response()->json([
            'status' => false,
            'message' => 'No se encontró el rol'
        ], 404);
    }

    $validated = $request->validate([
        'nombre' => 'required|string|unique:roles,nombre,' . $id,
        'descripcion' => 'nullable|string',
        'permisos' => 'sometimes|array', // Añade validación para permisos
        'permisos.*' => 'exists:permisos,id' // Valida que los permisos existan
    ]);

    $rol->update($validated);

    // Sincronizar permisos si existen en el request
    if ($request->has('permisos') && is_array($request->input('permisos'))) {
        $rol->permisos()->sync($request->input('permisos'));
    } else {
        // Opcional: limpiar permisos si no se envía nada
        $rol->permisos()->detach();
    }

    return response()->json([
        'status' => true,
        'message' => 'Rol actualizado correctamente',
        'rol' => $rol->load('permisos')
    ], 200);
}


    // Eliminar rol (solo si no tiene usuarios)
    public function destroy($id)
    {
        $rol = Rol::findOrFail($id);

        if (!$rol) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontró el rol'
            ], 404);
        }

        if ($rol->usuarios()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un rol asignado a usuarios.'
            ], 409);
        }

        $rol->delete();

        return response()->json([
            'status' => true,
            'message' => 'Rol eliminado satisfactoriamente',
            'rol' => $rol
        ], 200);
    }

    ///// Obtener permisos de un rol específico (agregador por ENRIQUE)
    public function permisos($id)
    {
        $rol = Rol::findOrFail($id);
        $permisos = $rol->permisos; // Relación muchos a muchos
    
        return response()->json([
            'status' => true,
            'data' => $permisos
        ]);
    }
    // // Asignar permisos a un rol
    // public function asignarPermisos(Request $request, $id)
    // {
    //     $rol = Rol::findOrFail($id);

    // $request->validate([
    //     'permisos' => 'required|array',
    //     'permisos.*' => 'exists:permisos,id',
    // ]);

    // $rol->permisos()->syncWithoutDetaching($request->permisos);

    // return response()->json([
    //     'message' => 'Permisos asignados correctamente',
    //     'rol' => $rol->load('permisos')
    // ]);
    // }

    // // Revocar un permiso específico de un rol
    // public function revocarPermiso($id, $permisoId)
    // {
    //     $rol = Rol::findOrFail($id);
    //     $rol->permisos()->detach($permisoId);

    //     return response()->json([
    //         'message' => 'Permiso revocado correctamente',
    //         'rol' => $rol->load('permisos')
    //     ]);
    // }
}
