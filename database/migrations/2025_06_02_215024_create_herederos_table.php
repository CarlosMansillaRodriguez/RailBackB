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
        Schema::create('herederos', function (Blueprint $table) {
            $table->id();
            $table->String('nombre_completo');
            $table->integer('ci');
            $table->String('parentesco');
            $table->date('fecha_registro');
            $table->integer('telefono');
            $table->integer('monto');
            $table->boolean('estado')->default(1);
            $table->foreignId('cuenta_id')->constrained('cuentas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('herederos');
    }
};
