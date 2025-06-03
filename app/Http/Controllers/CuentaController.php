<?php

namespace App\Http\Controllers;
use App\Models\Cuenta;
use Illuminate\Support\Str;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Http\Request;

class CuentaController extends Controller
{
    public function index()
    {
        // Obtener todas las cuentas
        //$cuentas = Cuenta::all();
        //return response()->json($cuentas);
        // Obtener todas las cuentas y cargar la relación con clientes
        $cuentas = Cuenta::with('cliente.usuario')->get();
        return response()->json($cuentas);
    }
    public function create()
    {
        // Aquí puedes implementar la lógica para mostrar el formulario de creación de una nueva cuenta
    }
    /* public function store(Request $request)
    {
        
        // Validación de datos
        $request->validate([
            'numero_cuenta' => 'required|string|unique:cuentas,numero_cuenta',
            'estado' => 'required|string',
            'fecha_de_apertura' => 'required|date',
            'saldo' => 'required|numeric|min:0',
            'tipo_de_cuenta' => 'required|string',
            'moneda' => 'required|string',
            'intereses' => 'required|numeric|between:0,99.99',
            'limite_de_retiro' => 'required|numeric|min:0',
            'cliente_id' => 'required|exists:clientes,id'
        ]);


        // Crear la cuenta
        $cuenta = Cuenta::create($request->all());
        
        if (!$cuenta) {
            return response()->json([
                'status' => false,
                'error' => 'No se pudo crear la cuenta',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cuenta creada satisfactoriamente',
            'cuenta' => $cuenta
        ], 201);
    }*/

    public function show($numero_cuenta)
    {
        // Obtener una cuenta específica
        $cuenta = Cuenta::findOrFail($numero_cuenta);

        if (!$cuenta) {
            return response()->json([
                'status' => false,
                'error' => 'No se encontró el cuenta',
            ], 404);
        }

        return response()->json($cuenta);
    } 
    public function store(Request $request)
    {
        // Validación de datos del usuario, cliente y cuenta
        $validatedData = $request->validate([
            // Datos del usuario
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'genero' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date|before:today',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'nombre_user' => 'required|string|max:255',
            /* |unique:usuarios,email */
            // Datos del cliente
            'ci' => 'required|integer',
            'telefono' => 'required|integer',
            'direccion' => 'required|string|max:255',
    
            // Datos de la cuenta
            'tipo_de_cuenta' => 'required|string',
            'moneda' => 'required|string|in:BOB,USD',
            'intereses' => 'required|numeric|between:0,99.99',
            'limite_de_retiro' => 'required|numeric|min:0',
            'saldo' => 'required|numeric|min:0',
    
            // Campos opcionales que pueden venir del frontend
            'numero_cuenta' => 'nullable|string|unique:cuentas,numero_cuenta',
            'fecha_de_apertura' => 'nullable|date',
        ]);
    
        try {
            // Verificar si el usuario ya existe o crearlo
            $usuario = Usuario::firstOrCreate(
                ['email' => $validatedData['email']],
                [
                    'nombre' => $validatedData['nombre'],
                    'apellido' => $validatedData['apellido'],
                    'genero' => $validatedData['genero'],
                    'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
                    'nombre_user' => $validatedData['nombre_user'],
                    'password' => bcrypt('default_password'),
                ]
            );
    
            // Verificar si el cliente ya existe o crearlo
            $cliente = Cliente::firstOrCreate(
                ['ci' => $validatedData['ci']],
                [
                    'telefono' => $validatedData['telefono'],
                    'direccion' => $validatedData['direccion'],
                    'usuario_id' => $usuario->id,
                ]
            );
    
            // Generar número de cuenta si no viene
            $numero_cuenta = $validatedData['numero_cuenta'] ?? null;
            if (!$numero_cuenta) {
                $numero_cuenta = 'CUE-' . strtoupper(Str::random(6));
                while (Cuenta::where('numero_cuenta', $numero_cuenta)->exists()) {
                    $numero_cuenta = 'CUE-' . strtoupper(Str::random(6));
                }
            }
    
            // Usar fecha de apertura si viene, sino hoy
            $fecha_de_apertura = $validatedData['fecha_de_apertura'] ?? now();
    
            // Crear la cuenta asociada al cliente
            $cuenta = Cuenta::create([
                'numero_cuenta' => $numero_cuenta,
                'estado' => 'Activa',
                'fecha_de_apertura' => $fecha_de_apertura,
                'saldo' => $validatedData['saldo'],
                'tipo_de_cuenta' => $validatedData['tipo_de_cuenta'],
                'moneda' => $validatedData['moneda'],
                'intereses' => $validatedData['intereses'],
                'limite_de_retiro' => $validatedData['limite_de_retiro'],
                'cliente_id' => $cliente->id,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Usuario, cliente y cuenta creados correctamente.',
                'data' => [
                    'usuario' => $usuario,
                    'cliente' => $cliente,
                    'cuenta' => $cuenta,
                ]
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Error al crear el registro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        // Aquí puedes implementar la lógica para mostrar el formulario de edición de una cuenta
    }
    /* public function update(Request $request, $numero_cuenta)
    {
        // Validación de datos
        $request->validate([
            'numero_cuenta' => 'required|string|unique:cuentas,numero_cuenta',
            'estado' => 'required|string',
            'fecha_apertura' => 'required|date',
            'saldo' => 'required|numeric|min:0',
            'tipo_cuenta' => 'required|string',
            'moneda' => 'required|string',
            'intereses' => 'required|numeric|between:0,99.99',
            'limite_retiro_diario' => 'required|integer|min:0',
        ]);

        // Actualizar la cuenta
        $cuenta = Cuenta::findOrFail($numero_cuenta);

        if (!$cuenta) {
            return response()->json([
                'status' => false,
                'error' => 'No se encontró el cuenta',
            ], 404);
        }

        $cuenta->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Cuenta actualizado satisfactoriamente',
            'cuenta' => $cuenta
        ], 200);
    } */

    public function update(Request $request, string $id)
{
    // Buscar la cuenta por ID
    $cuenta = Cuenta::with('cliente.usuario')->findOrFail($id);
    $usuarioId = $cuenta->cliente->usuario->id;
    $clienteId = $cuenta->cliente->id;
    
    // Validación similar a store, pero permitiendo valores únicos excepto para el registro actual
    $validatedData = $request->validate([
        // Datos del usuario
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'genero' => 'required|in:M,F',
        'fecha_nacimiento' => 'required|date|before:today',
        'email' => "required|email|unique:usuarios,email,$usuarioId",
        'password' => 'nullable|string|min:6', // Opcional en edición
        'nombre_user' => 'required|string|max:255',

        // Datos del cliente
        'ci' => "required|integer|unique:clientes,ci,$clienteId",
        'telefono' => 'required|integer',
        'direccion' => 'required|string|max:255',

        // Datos de la cuenta
        'tipo_de_cuenta' => 'required|string',
        'moneda' => 'required|string|in:BOB,USD',
        'intereses' => 'required|numeric|between:0,99.99',
        'limite_de_retiro' => 'required|numeric|min:0',
        'saldo' => 'required|numeric|min:0',
        'numero_cuenta' => 'nullable|string|unique:cuentas,numero_cuenta,' . $cuenta->id,
        'fecha_de_apertura' => 'nullable|date',
    ]);

    try {
        

        // Actualizar datos de la cuenta
        $cuenta->numero_cuenta = $validatedData['numero_cuenta'] ?? $cuenta->numero_cuenta;
        $cuenta->fecha_de_apertura = $validatedData['fecha_de_apertura'] ?? now();
        $cuenta->saldo = $validatedData['saldo'];
        $cuenta->tipo_de_cuenta = $validatedData['tipo_de_cuenta'];
        $cuenta->moneda = $validatedData['moneda'];
        $cuenta->intereses = $validatedData['intereses'];
        $cuenta->limite_de_retiro = $validatedData['limite_de_retiro'];
        $cuenta->save();

        // Obtener cliente y usuario asociados
        $cliente = $cuenta->cliente;
        $usuario = $cliente->usuario;

        // Actualizar usuario
        $usuario->nombre = $validatedData['nombre'];
        $usuario->apellido = $validatedData['apellido'];
        $usuario->genero = $validatedData['genero'];
        $usuario->fecha_nacimiento = $validatedData['fecha_nacimiento'];
        $usuario->nombre_user = $validatedData['nombre_user'];
        $usuario->email = $validatedData['email'];

        if (!empty($validatedData['password'])) {
            $usuario->password = bcrypt($validatedData['password']); // Solo cambiar si se envía nueva contraseña
        }

        $usuario->save();

        // Actualizar cliente
        $cliente->ci = $validatedData['ci'];
        $cliente->telefono = $validatedData['telefono'];
        $cliente->direccion = $validatedData['direccion'];
        $cliente->save();

        return response()->json([
            'status' => true,
            'message' => 'Usuario, cliente y cuenta actualizados correctamente.',
            'data' => [
                'usuario' => $usuario,
                'cliente' => $cliente,
                'cuenta' => $cuenta,
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error' => 'Error al actualizar el registro: ' . $e->getMessage()
        ], 500);
    }
}
    public function destroy(string $id)
    {
        //$this->autorizar();
        /* $tarjeta = Tarjeta::findOrFail($id);
        $tarjeta->delete();
        return response()->json(['message' => 'Tarjeta eliminada']); */
        
            try {
                // Buscar la tarjeta por ID
                $cuenta = Cuenta::find($id);
        
                if (!$cuenta) {
                    return response()->json([
                        'status' => false,
                        'error' => 'Cuenta no encontrada'
                    ], 404);
                }
        
                // Alternar el estado entre "Activado" y "Desactivado"
                $nuevoEstado = $cuenta->estado === 'Activado' ? 'Desactivado' : 'Activado';
        
                // Actualizar y guardar
                $cuenta->estado = $nuevoEstado;
                $cuenta->save();
        
                return response()->json([
                    'status' => true,
                    'message' => $nuevoEstado === 'Activado' 
                        ? 'cuenta reactivada correctamente' 
                        : 'cuenta desactivada correctamente',
                    'cuenta' => $cuenta,
                    'nuevo_estado' => $nuevoEstado
                ]);
        
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error al cambiar el estado de la cuenta',
                    'details' => $e->getMessage()
                ], 500);
            }
    }
	    /////////////////////////////////////////////////////////
    public function obtenerDetalles($numero)
    {
        $cuenta = Cuenta::with(['cliente.usuario'])->where('numero_cuenta', $numero)->first();

        if (!$cuenta) {
            return response()->json([
                'success' => false,
                'message' => 'La cuenta no existe.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cuenta->id,
                'saldo' => $cuenta->saldo,
                'usuario' => [
                    'nombre' => $cuenta->cliente->usuario->nombre,
                    'apellido' => $cuenta->cliente->usuario->apellido,
                ]
            ]
        ]);
    }

	
	
    public function getCuentasByCliente($clienteId)
    {
        // Obtener todas las cuentas de un cliente específico
        $cuentas = Cuenta::where('cliente_id', $clienteId)->get();

        return response()->json($cuentas);
    }

    public function getCuentasByEstado($estado)
    {
        // Obtener todas las cuentas con un estado específico
        $cuentas = Cuenta::where('estado', $estado)->get();

        return response()->json($cuentas);
    }
    public function getCuentasByTipo($tipo)
    {
        // Obtener todas las cuentas de un tipo específico
        $cuentas = Cuenta::where('tipo_cuenta', $tipo)->get();

        return response()->json($cuentas);
    }
    public function getCuentasByMoneda($moneda)
    {
        // Obtener todas las cuentas de una moneda específica
        $cuentas = Cuenta::where('moneda', $moneda)->get();

        return response()->json($cuentas);
    }
    public function getCuentasByFechaApertura($fecha)
    {
        // Obtener todas las cuentas abiertas en una fecha específica
        $cuentas = Cuenta::whereDate('fecha_apertura', $fecha)->get();

        return response()->json($cuentas);
    }
    public function getCuentasByFechaCierre($fecha)
    {
        // Obtener todas las cuentas cerradas en una fecha específica
        $cuentas = Cuenta::whereDate('fecha_cierre', $fecha)->get();

        return response()->json($cuentas);
    }

    //añadido para flutter Cuenta controller
    public function updateSaldo(Request $request, $id)
    {
        try {
            $cuenta = Cuenta::findOrFail($id);

            // Validar el nuevo saldo (con JSON forzado)
            $validator = \Validator::make($request->all(), [
                'saldo' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Error de validación',
                    'messages' => $validator->errors(),
                ], 422);
            }

            // Actualizar saldo
            $cuenta->saldo = $request->saldo;
            $cuenta->save();

            return response()->json([
                'message' => 'Saldo actualizado correctamente',
                'cuenta' => $cuenta
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el saldo: ' . $e->getMessage()
            ], 500);
        }
    }

}
