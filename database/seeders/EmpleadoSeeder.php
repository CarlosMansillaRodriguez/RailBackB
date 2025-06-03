<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Empleado;
use Illuminate\Support\Facades\Hash;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario para el cliente
        $usuario = Usuario::create([
            'nombre_user' => 'Juan Empleado',
            'email' => 'juan@empleado.com',
            'password' => Hash::make('empleado123'),
            'nombre' => 'Juan',
            'apellido' => 'Ramon',
            'fecha_nacimiento' => '1980-01-01',
            'genero' => 'M',
            'estado'=> 1,
        ]);

        // Crear empleado asociado al usuario
        Empleado::create([
            'fecha_contrato' => '2023-01-01',
            'cargo' => 'Cajero 1',
            'horario_entrada' => '08:00',
            'horario_salida' => '16:00',
            'usuario_id' => $usuario->id,
        ]);
    }
}
