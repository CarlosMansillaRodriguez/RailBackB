<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('celulares', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('modelo');
            $table->timestamps();
        });

        Schema::table('cuentas', function (Blueprint $table) {
            $table->foreignId('celular_id')->nullable()->constrained('celulares')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('cuentas', function (Blueprint $table) {
            $table->dropForeign(['celular_id']);
            $table->dropColumn('celular_id');
        });

        Schema::dropIfExists('celulares');
    }
};
