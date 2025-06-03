<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario para el cliente
        $usuario = Usuario::create([
            'nombre_user' => 'Pedro Cliente',
            'email' => 'pedro@cliente.com',
            'password' => Hash::make('cliente123'),
            'nombre' => 'Pedro',
            'apellido' => 'Gómez',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'estado'=> 1,
        ]);

        // Crear cliente asociado al usuario
        Cliente::create([
            'ci' => '9876543',
            'direccion' => 'Barrio Mutualista',
            'telefono' => '76438291',
            'usuario_id' => $usuario->id,
        ]);
    }
}
