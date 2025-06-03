<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_cuenta')->unique();
            $table->string('estado');//v2
            $table->date('fecha_de_apertura');
            $table->decimal('saldo',12,2);
            $table->string('tipo_de_cuenta');
            $table->string('moneda');
            $table->decimal('intereses',5,2);
            $table->decimal('limite_de_retiro',12,2);
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
