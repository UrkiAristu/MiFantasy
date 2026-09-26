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
        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn('jugadores_por_equipo');
            $table->enum('modalidad', ['11', '7', 'sala'])->default('11')->after('estado');
        });

        Schema::table('alineaciones', function (Blueprint $table) {
            $table->string('formacion', 20)->nullable()->after('jornada_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alineaciones', function (Blueprint $table) {
            $table->dropColumn('formacion');
        });

        Schema::table('torneos', function (Blueprint $table) {
            $table->dropColumn('modalidad');
            $table->unsignedTinyInteger('jugadores_por_equipo')->default(5)->after('estado');
        });
    }
};
