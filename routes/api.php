<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\AtmController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\TarjetaController;
use App\Http\Controllers\CelularController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\ReposicionController;
use App\Http\Controllers\HerederoController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\TransaccionController;


// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Sesión
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // Usuarios y roles
    Route::apiResource('usuarios', UsuarioController::class);
    Route::post('/usuarios/{id}/roles', [UsuarioController::class, 'asignarRoles']);
    Route::post('/usuarios/{id}/roles/revocar', [UsuarioController::class, 'revocarRoles']);

    // Empleados
    Route::apiResource('empleados', EmpleadoController::class);

    // Clientes
    Route::apiResource('clientes', ClienteController::class);

    // Cuentas
    Route::apiResource('cuentas', CuentaController::class);
    Route::get('/cuentas/obtener-detalles/{numero}', [CuentaController::class, 'obtenerDetalles']);
       // CRUD de HEREDEROS
    Route::apiResource('herederos', HerederoController::class);

    // CRUD de Transacciones
    Route::apiResource('transacciones', TransaccionController::class);

    // Roles y permisos
    Route::apiResource('roles', RolController::class);
    Route::get('/roles/{id}/permisos', [RolController::class, 'permisos']);

    Route::apiResource('permisos', PermisoController::class);
    Route::post('/permisos/asignar', [PermisoController::class, 'asignarPermiso']);
    Route::post('/permisos/desasignar', [PermisoController::class, 'desasignarPermiso']);
    Route::get('/permisos/rol/{rol_id}', [PermisoController::class, 'obtenerPermisos']);

    // Bitácora
    Route::apiResource('bitacoras', BitacoraController::class);

    // ATM y Técnico
    Route::apiResource('atms', AtmController::class);
    Route::apiResource('tecnicos', TecnicoController::class);

    // Tarjetas
    Route::get('/tarjetas', [TarjetaController::class, 'index']);
    Route::post('/tarjetas', [TarjetaController::class, 'store']);
    Route::get('/tarjetas/{id}', [TarjetaController::class, 'show']);
    Route::put('/tarjetas/{id}', [TarjetaController::class, 'update']);
    Route::delete('/tarjetas/{id}', [TarjetaController::class, 'destroy']);

    // Celulares
    Route::post('/celular/registrar-sin-token', [CelularController::class, 'registrarConCredenciales']);

    // Reportes
    Route::apiResource('reportes', ReporteController::class);

    // Incidencias
    Route::apiResource('incidencias', IncidenciaController::class);

    // Reposiciones
    Route::get('/reposiciones', [ReposicionController::class, 'index']);
    Route::post('/reposiciones', [ReposicionController::class, 'store']);
});

// Comprobaciones
Route::get('/db-check', function () {
    return response()->json([
        'base_de_datos_activa' => DB::connection()->getDatabaseName()
    ]);
});

Route::get('/verificar', function () {
    return response()->json(['mensaje' => 'Ruta funcionando correctamente']);
});

// FLUTTER 

// PRUEBAS

Route::middleware('auth:sanctum')->post('/stripe/create-intent', [StripeController::class, 'createIntent']);

Route::middleware('auth:sanctum')->put('/cuentas/{id}/saldo', [CuentaController::class, 'updateSaldo']);