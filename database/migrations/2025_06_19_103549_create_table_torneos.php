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
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('logo')->nullable(); // Optional logo field
            $table->string('estado')->default('activo'); // Default state is 'active'
            $table->unsignedTinyInteger('jugadores_por_equipo')->default(5)
                ->comment('Número de jugadores por equipo que juegan a la vez');
            $table->boolean('usa_posiciones')->default(false)
                ->comment('Indica si se usan posiciones en alineaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torneos');
    }
};
