<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incidencia;

class IncidenciaSeeder extends Seeder
{
    public function run(): void
    {
        Incidencia::create([
            'descripcion' => 'El cajero no entrega efectivo',
            'estado' => 1,
            'fecha_reporte' => now()->toDateString(),
            'fecha_solucion' => null,
            'tipo' => 'hardware',
            'cliente_id' => 1,
            'tecnico_id' => 1,
        ]);
    }
}
