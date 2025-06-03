<?php

// database/seeders/AtmSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atm;

class AtmSeeder extends Seeder
{
    public function run(): void
    {
        Atm::create([
            'ciudad' => 'Santa Cruz',
            'estado' => 'operativo',
            'fecha_repo' => now()->toDateString(),
            'saldo' => 5000,
            'ubicacion' => 'Av. Cañoto, Zona Central',
        ]);

        Atm::create([
            'ciudad' => 'La Paz',
            'estado' => 'operativo',
            'fecha_repo' => now()->subDays(2)->toDateString(),
            'saldo' => 7000,
            'ubicacion' => 'Av. Mariscal Santa Cruz, Centro',
        ]);

        Atm::create([
            'ciudad' => 'Cochabamba',
            'estado' => 'mantenimiento',
            'fecha_repo' => now()->subDays(5)->toDateString(),
            'saldo' => 0,
            'ubicacion' => 'Plaza Colón',
        ]);
    }
}
