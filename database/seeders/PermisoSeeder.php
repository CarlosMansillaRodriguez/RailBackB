<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            //Clientes
            ['nombre' => 'ver-cliente', 'descripcion' => 'Permite ver la lista de clientes'],
            ['nombre' => 'crear-cliente', 'descripcion' => 'Permite crear nuevos clientes'],
            ['nombre' => 'editar-cliente', 'descripcion' => 'Permite editar la información de los clientes'],
            ['nombre' => 'eliminar-cliente', 'descripcion' => 'Permite eliminar clientes'],
            //Cuentas
            ['nombre' => 'ver-cuenta', 'descripcion' => 'Permite ver la lista de cuentas'],
            ['nombre' => 'crear-cuenta', 'descripcion' => 'Permite crear nuevas cuentas'],
            ['nombre' => 'editar-cuenta', 'descripcion' => 'Permite editar la información de las cuentas'],
            ['nombre' => 'eliminar-cuenta', 'descripcion' => 'Permite eliminar cuentas'],
            //Transacciones
            ['nombre' => 'ver-transaccion', 'descripcion' => 'Permite ver las transacciones realizadas'],
            ['nombre' => 'crear-transaccion', 'descripcion' => 'Permite crear nuevas transacciones'],
            ['nombre' => 'editar-transaccion', 'descripcion' => 'Permite editar transacciones existentes'],
            ['nombre' => 'eliminar-transaccion', 'descripcion' => 'Permite eliminar transacciones'],
            //Bitácora
            ['nombre' => 'ver-bitacora', 'descripcion' => 'Permite ver la bitácora del sistema'],
            //Reportes
            ['nombre' => 'ver-reporte', 'descripcion' => 'Permite ver los reportes generados'],
            ['nombre' => 'crear-reporte', 'descripcion' => 'Permite crear nuevos reportes'],
            ['nombre' => 'eliminar-reporte', 'descripcion' => 'Permite eliminar reportes'],
            //Usuarios
            ['nombre' => 'ver-usuario', 'descripcion' => 'Permite ver la lista de usuarios'],
            ['nombre' => 'crear-usuario', 'descripcion' => 'Permite crear nuevos usuarios'],
            ['nombre' => 'editar-usuario', 'descripcion' => 'Permite editar la información de los usuarios'],
            ['nombre' => 'eliminar-usuario', 'descripcion' => 'Permite eliminar usuarios'],
            //Roles
            ['nombre' => 'ver-rol', 'descripcion' => 'Permite ver la lista de roles'],
            ['nombre' => 'crear-rol', 'descripcion' => 'Permite crear nuevos roles'],
            ['nombre' => 'editar-rol', 'descripcion' => 'Permite editar roles existentes'],
            ['nombre' => 'eliminar-rol', 'descripcion' => 'Permite eliminar roles'],
            //Tarjetas
            ['nombre' => 'ver-tarjeta', 'descripcion' => 'Permite ver la lista de tarjetas'],
            ['nombre' => 'crear-tarjeta', 'descripcion' => 'Permite crear nuevas tarjetas'],
            ['nombre' => 'editar-tarjeta', 'descripcion' => 'Permite editar tarjetas existentes'],
            ['nombre' => 'eliminar-tarjeta', 'descripcion' => 'Permite eliminar tarjetas'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::create([
                'nombre' => $permiso['nombre'],
                'descripcion' => $permiso['descripcion']
            ]);
        }
    }
}