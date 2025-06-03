<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_transaccion')->unique(); // Código único de la transacción
            $table->unsignedBigInteger('id_cuenta_origen')->nullable(); // Cuenta desde donde sale el dinero
            $table->unsignedBigInteger('id_cuenta_destino')->nullable(); // Cuenta hacia donde va el dinero
            $table->decimal('monto', 15, 2); // Monto de la transacción
            $table->string('tipo_transaccion'); // Ej: 'Depósito', 'Retiro', 'Transferencia'
            $table->string('estado_transaccion')->default('pendiente'); // Nuevo campo
            $table->timestamp('fecha_transaccion')->useCurrent(); // Fecha y hora de la transacción
            $table->text('descripcion')->nullable(); // Descripción adicional si es necesario
            $table->timestamps();
            //si el "tipo_transaccion"="Depósito", id_cuenta_origen debe ser nulo
            //si el "tipo_transaccion"="Retiro", id_cuenta_destino debe ser nulo
            //si el "tipo_transaccion"="Transferencia", id_cuenta_origen y id_cuenta_destino son diferentes de nulo
            // Claves foráneas
            $table->foreign('id_cuenta_origen')
                ->references('id')
                ->on('cuentas')
                ->onDelete('restrict');

            $table->foreign('id_cuenta_destino')
                ->references('id')
                ->on('cuentas')
                ->onDelete('set null');

        });
        // Restricción CHECK para garantizar que al menos uno no sea NULL
            DB::statement('
                ALTER TABLE transacciones
                ADD CONSTRAINT chk_cuentas_not_both_null
                CHECK (
                    id_cuenta_origen IS NOT NULL OR id_cuenta_destino IS NOT NULL
                )
            ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la restricción antes de borrar la tabla
        DB::statement('ALTER TABLE transacciones DROP CONSTRAINT IF EXISTS chk_cuentas_not_both_null');

        Schema::dropIfExists('transacciones');
    }
};