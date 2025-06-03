<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tecnico;
use App\Models\Usuario;

class TecnicoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un usuario para técnico
        $usuario = Usuario::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'nombre_user' => 'tecnico1',
            'email' => 'tecnico1@banco.com',
            'password' => bcrypt('tecnico123'),
            'genero' => 'M',
            'estado' => 1,
            'fecha_nacimiento' => '1995-05-05',
        ]);

        Tecnico::create([
            'nombre_empresa' => 'TechService',
            'telefono' => '78965412',
            'usuario_id' => $usuario->id,
        ]);
    }
}
