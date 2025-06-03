<?php

// database/seeders/ReposicionDetalleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reposicion;
use App\Models\ReposicionDetalle;

class ReposicionDetalleSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener una reposición
        $reposicion = Reposicion::first(); // asumiendo que ya se creó al menos una

        if (!$reposicion) {
            $this->command->warn('⚠️ No hay reposiciones creadas. Ejecuta ReposicionSeeder primero.');
            return;
        }

        // Insertar detalles
        $detalles = [
            ['cantidad' => 6, 'denominacion' => 20, 'subtotal' => 120],
            ['cantidad' => 1, 'denominacion' => 100, 'subtotal' => 100],
            ['cantidad' => 4, 'denominacion' => 50, 'subtotal' => 200],
        ];

        foreach ($detalles as $detalle) {
            $reposicion->detalles()->create($detalle);
        }
    }
}
