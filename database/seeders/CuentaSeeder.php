<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cuenta;
use App\Models\Cliente;
use Illuminate\Support\Str;

class CuentaSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los clientes disponibles
        $clientes = Cliente::all();

        // Si no hay clientes, no podemos crear cuentas
        if ($clientes->isEmpty()) {
            $this->command->warn('⚠️ No se encontraron clientes. Se deben crear clientes primero.');
            return;
        }

        foreach ($clientes as $cliente) {
            Cuenta::create([
                'numero_cuenta' => Str::random(10),
                'estado' => 1,
                'fecha_de_apertura' => now()->subDays(rand(1, 365)),
                'saldo' => rand(1000, 10000),
                'tipo_de_cuenta' => 'Ahorro',
                'moneda' => 'BOB',
                'intereses' => 2.5, // cuidado: respeta el campo mal escrito si no lo cambiaste aún
                'limite_de_retiro' => 2000,
                'cliente_id' => $cliente->id,
            ]);
        }

        $this->command->info('✅ Cuentas generadas correctamente para todos los clientes.');
    }
}
