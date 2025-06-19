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
        Schema::create('equipo_jugador_torneo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('torneo_id')->constrained()->onDelete('cascade');

            $table->integer('goles')->default(0);
            $table->integer('asistencias')->default(0);
            $table->integer('puntos')->default(0);

            $table->timestamps();

            $table->unique(['jugador_id', 'equipo_id', 'torneo_id']); // Evitar registros duplicados
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipo_jugador_torneo');
    }
};
