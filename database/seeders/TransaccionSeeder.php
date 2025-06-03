<?php

namespace Database\Seeders;

use App\Models\Transaccion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TransaccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*Transaccione::create([
            'codigo_transaccion' => 'TX-' . now()->format('Ymd') . '-' . Str::random(6),
            'id_cuenta_origen' => 2,
            'id_cuenta_destino' => 3,
            'monto' => 1500.00,
            'tipo_transaccion' => 'Transferencia',
            'estado_transaccion' => 'pendiente',
            'fecha_transaccion' => Carbon::now(),
            'descripcion' => 'Transferencia entre cuentas personales',

        ]);
        */
        Transaccion::create([
            'codigo_transaccion' => 'TX-' . now()->format('Ymd') . '-' . Str::random(6),
            'id_cuenta_origen' => 1,
            'id_cuenta_destino' => null,
            'monto' => 500.00,
            'tipo_transaccion' => 'Retiro',
            'estado_transaccion' => 'completada',
            'fecha_transaccion' => Carbon::now()->subDays(1),
            'descripcion' => 'Retiro de efectivo',
        ]);

        Transaccion::create([
            'codigo_transaccion' => 'TX-' . now()->format('Ymd') . '-' . Str::random(6),
            'id_cuenta_origen' => null,
            'id_cuenta_destino' => 1, // La misma cuenta se deposita dinero
            'monto' => 2500.00,
            'tipo_transaccion' => 'Depósito',
            'estado_transaccion' => 'completada',
            'fecha_transaccion' => Carbon::now()->subDays(2),
            'descripcion' => 'Depósito en la misma cuenta',
        ]);
    }
}
