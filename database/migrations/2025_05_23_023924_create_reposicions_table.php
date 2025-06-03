<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reposicions', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('monto_repuesto', 10, 2);
            $table->unsignedBigInteger('atm_id');
            $table->timestamps();

            $table->foreign('atm_id')->references('id')->on('atms')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('reposicions');
    }
};
