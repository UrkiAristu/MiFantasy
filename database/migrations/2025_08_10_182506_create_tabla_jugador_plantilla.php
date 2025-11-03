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
        Schema::create('jugador_plantilla', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_id')->constrained()->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->string('posicion')->nullable();
            $table->timestamps();

            $table->unique(['plantilla_id', 'jugador_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jugador_plantilla');
    }
};
