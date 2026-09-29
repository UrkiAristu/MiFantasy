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
        // 1. Eliminar tabla legacy 'cuentas' sustituida por 'users'
        Schema::dropIfExists('cuentas');

        // 2. Añadir índice compuesto en jornadas para consultas de congelación del scheduler
        Schema::table('jornadas', function (Blueprint $table) {
            $table->index(['fecha_cierre_alineaciones', 'alineaciones_congeladas'], 'jornadas_cierre_congeladas_idx');
        });

        // 3. Añadir índice compuesto en partidos para filtrado por jornada y estado de partido
        Schema::table('partidos', function (Blueprint $table) {
            $table->index(['jornada_id', 'estado'], 'partidos_jornada_estado_idx');
        });

        // 4. Añadir índice en alineacion_jugador para acelerar consultas y agregaciones por jugador
        Schema::table('alineacion_jugador', function (Blueprint $table) {
            $table->index('jugador_id', 'alineacion_jugador_jugador_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alineacion_jugador', function (Blueprint $table) {
            $table->dropIndex('alineacion_jugador_jugador_id_idx');
        });

        Schema::table('partidos', function (Blueprint $table) {
            $table->dropIndex('partidos_jornada_estado_idx');
        });

        Schema::table('jornadas', function (Blueprint $table) {
            $table->dropIndex('jornadas_cierre_congeladas_idx');
        });

        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('nombreUsuario', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->boolean('activo')->default(true);
            $table->boolean('admin')->default(false);
            $table->timestamps();
        });
    }
};
