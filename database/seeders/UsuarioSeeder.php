<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        Usuario::firstOrCreate([   
            'email' => 'admin@banco.com' ,        
            'nombre_user' => 'admin',
            'password' => Hash::make('12345678'),
            'nombre' => 'Admin',
            'apellido' => 'Banco',
            'genero' => 'M',
            'fecha_nacimiento' => '1990-01-01',
            'estado' => 1,
        ]);
    }
}
