<?php

namespace App\Http\Controllers;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::all();
        return response()->json([
            'status' => true,
            'message' => 'Lista de permisos obtenida correctamente.',
            'permisos' => $permisos
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|unique:permisos,nombre',
        'descripcion' => 'nullable|string'
    ]);

    $permiso = Permiso::create($request->only(['nombre', 'descripcion']));

    return response()->json([
        'status' => true,
        'message' => 'Permiso creado correctamente',
        'permiso' => $permiso
    ], 201);
}

public function update(Request $request, $id)
{
    $permiso = Permiso::find($id);

    if (!$permiso) {
        return response()->json(['error' => 'Permiso no encontrado'], 404);
    }

    $request->validate([
        'nombre' => 'string|unique:permisos,nombre,' . $id,
        'descripcion' => 'nullable|string|max:255',
    ]);

    $permiso->update($request->only(['nombre', 'descripcion']));

    return response()->json([
        'message' => 'Permiso actualizado correctamente',
        'permiso' => $permiso
    ]);
}

public function destroy(string $id)
    {
        // Buscar el permiso por ID
        $permiso = Permiso::find($id);

        if (!$permiso) {
            return response()->json([
                'status' => false,
                'error' => 'Permiso no encontrado'
            ], 404);
        }

        try {
            // Eliminar físicamente el registro
            $permiso->delete();

            return response()->json([
                'status' => true,
                'message' => 'Permiso eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al eliminar el permiso',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function asignarPermiso(Request $request)
    {
        $rol = Rol::find($request->rol_id);
        $permiso = Permiso::find($request->permiso_id);

        if (!$rol || !$permiso) {
            return response()->json(['error' => 'Rol o permiso no encontrado'], 404);
        }

        // Asigna el permiso al rol
        $rol->permisos()->attach($permiso->id);
        return response()->json(['message' => 'Permiso asignado correctamente'], 200);
    }

    public function desasignarPermiso(Request $request)
    {
        $rol = Rol::find($request->rol_id);
        $permiso = Permiso::find($request->permiso_id);

        if (!$rol || !$permiso) {
            return response()->json(['error' => 'Rol o permiso no encontrado'], 404);
        }

        // Desasigna el permiso del rol utilizando detach en lugar de attach
        $rol->permisos()->detach($permiso->id);
        return response()->json(['message' => 'Permiso desasignado correctamente'], 200);
    }

    public function obtenerPermisos($rol_id)
    {
        $rol = Rol::find($rol_id);

        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        // Obtiene los permisos del rol
        $permisos = $rol->permisos;
        return response()->json($permisos);
    }

    public function asignarTodosLosPermisos($rol_id)
    {
        $rol = Rol::find($rol_id);
        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        // Obtén todos los permisos disponibles
        $permisos = Permiso::all();
        // $permisos = Permiso::pluck('id')->toArray();

        // Asigna todos los permisos al rol
        $rol->permisos()->sync($permisos);

        return response()->json([
            'message' => 'Se han asignado todos los permisos al rol correctamente.',
        ]);
    }

    public function desasignarTodosLosPermisos($rol_id)
    {
        $rol = Rol::find($rol_id);
        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        // Desasigna todos los permisos del rol
        $rol->permisos()->detach();

        return response()->json([
            'message' => 'Se han desasignado todos los permisos del rol correctamente.',
        ]);
    }

    public function tienePermiso($rolId, $permiso)
    {
        $rol = Rol::find($rolId);

        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        $tienePermiso = $rol->permisos->contains('nombre', $permiso);

        return response()->json(['tienePermiso' => $tienePermiso]);
    }
}
