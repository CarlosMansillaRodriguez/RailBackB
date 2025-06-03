<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reposicion_detalles', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->decimal('denominacion', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->unsignedBigInteger('reposicion_id');
            $table->timestamps();

            $table->foreign('reposicion_id')->references('id')->on('reposicions')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('reposicion_detalles');
    }
};
