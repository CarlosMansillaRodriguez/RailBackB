<?php

// database/seeders/ReposicionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reposicion;

class ReposicionSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate de que exista al menos un ATM
        $atm_id = 1; // ajusta si es necesario

        Reposicion::create([
            'fecha' => now()->toDateString(),
            'monto_repuesto' => 420.00,
            'atm_id' => $atm_id,
        ]);
    }
}

