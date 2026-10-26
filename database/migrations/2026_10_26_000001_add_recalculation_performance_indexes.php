<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Optimiza índices clave para garantizar tiempos de respuesta < 200ms
     * en el recálculo masivo y atómico de puntos (RecalcularPuntosJornadaJob).
     */
    public function up(): void
    {
        // 1. alineacion_jugador: Índice con jugador_id al frente para acelerar el JOIN con las estadísticas del partido
        Schema::table('alineacion_jugador', function (Blueprint $table) {
            $table->index(['jugador_id', 'alineacion_id'], 'idx_alineacion_jugador_lookup');
        });

        // 2. alineaciones: Índice compuesto para acelerar el filtrado y uniones por jornada y liguilla
        Schema::table('alineaciones', function (Blueprint $table) {
            $table->index(['jornada_id', 'liguilla_id'], 'idx_alineaciones_jornada_liguilla');
        });

        // 3. partidos: Índice para acelerar la búsqueda de partidos por jornada en el job de recálculo
        Schema::table('partidos', function (Blueprint $table) {
            $table->index(['jornada_id', 'id'], 'idx_partidos_jornada_id');
        });

        // 4. estadisticas: Índice cubierto (covering index) incluyendo puntos para agregaciones SUM ultra rápidas
        Schema::table('estadisticas', function (Blueprint $table) {
            $table->index(['partido_id', 'jugador_id', 'puntos'], 'idx_estadisticas_recalculo_puntos');
        });

        // 5. liguilla_usuario: Índice optimizado para el bulk update de clasificación global por torneo/liguilla
        Schema::table('liguilla_usuario', function (Blueprint $table) {
            $table->index(['liguilla_id', 'user_id', 'puntos'], 'idx_liguilla_usuario_puntos_calc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liguilla_usuario', function (Blueprint $table) {
            $table->dropIndex('idx_liguilla_usuario_puntos_calc');
        });

        Schema::table('estadisticas', function (Blueprint $table) {
            $table->dropIndex('idx_estadisticas_recalculo_puntos');
        });

        Schema::table('partidos', function (Blueprint $table) {
            $table->dropIndex('idx_partidos_jornada_id');
        });

        Schema::table('alineaciones', function (Blueprint $table) {
            $table->dropIndex('idx_alineaciones_jornada_liguilla');
        });

        Schema::table('alineacion_jugador', function (Blueprint $table) {
            $table->dropIndex('idx_alineacion_jugador_lookup');
        });
    }
};
