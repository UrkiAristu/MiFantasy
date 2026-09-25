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
        // 1. Tabla liguilla_usuario: unicidad de usuario en liguilla e índice para ordenación de clasificación por puntos
        Schema::table('liguilla_usuario', function (Blueprint $table) {
            $table->unique(['liguilla_id', 'user_id']);
            $table->index(['liguilla_id', 'puntos']);
        });

        // 2. Tabla estadisticas: unicidad de estadísticas por jugador en cada partido e índice compuesto para agregaciones
        Schema::table('estadisticas', function (Blueprint $table) {
            $table->unique(['partido_id', 'jugador_id']);
            $table->index(['jugador_id', 'partido_id']);
        });

        // 3. Tabla equipo_jugador_torneo: índices compuestos para búsquedas por torneo/equipo y jugador/torneo
        Schema::table('equipo_jugador_torneo', function (Blueprint $table) {
            $table->index(['torneo_id', 'equipo_id']);
            $table->index(['jugador_id', 'torneo_id']);
        });

        // 4. Tabla alineaciones: índices compuestos para filtrado de jornadas por liguilla y usuario por liguilla
        Schema::table('alineaciones', function (Blueprint $table) {
            $table->index(['liguilla_id', 'jornada_id']);
            $table->index(['liguilla_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alineaciones', function (Blueprint $table) {
            $table->dropIndex(['liguilla_id', 'jornada_id']);
            $table->dropIndex(['liguilla_id', 'user_id']);
        });

        Schema::table('equipo_jugador_torneo', function (Blueprint $table) {
            $table->dropIndex(['torneo_id', 'equipo_id']);
            $table->dropIndex(['jugador_id', 'torneo_id']);
        });

        Schema::table('estadisticas', function (Blueprint $table) {
            $table->dropUnique(['partido_id', 'jugador_id']);
            $table->dropIndex(['jugador_id', 'partido_id']);
        });

        Schema::table('liguilla_usuario', function (Blueprint $table) {
            $table->dropUnique(['liguilla_id', 'user_id']);
            $table->dropIndex(['liguilla_id', 'puntos']);
        });
    }
};
