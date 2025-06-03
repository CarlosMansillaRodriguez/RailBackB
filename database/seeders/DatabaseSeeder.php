<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // User::factory(10)->create();


    // User::factory()->create([
    //     'name' => 'Test User',
    //     'email' => 'test@example.com',
    // ]);

    // ✅ Dejá solo esto:
    $this->call([
        
        UsuarioSeeder::class,
        EmpleadoSeeder::class,
        ClienteSeeder::class,
        CuentaSeeder::class,
        RolSeeder::class,
        TecnicoSeeder::class,
        IncidenciaSeeder::class,
        AtmSeeder::class,
        ReposicionSeeder::class,
        ReposicionDetalleSeeder::class,
    ]);
    
        
    
}


}
