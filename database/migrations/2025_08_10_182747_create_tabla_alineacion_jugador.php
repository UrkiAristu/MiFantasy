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
        Schema::create('alineacion_jugador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alineacion_id')->constrained('alineaciones')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->integer('puntos')->default(0);
            $table->timestamps();

            $table->unique(['alineacion_id', 'jugador_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alineacion_jugador');
    }
};
