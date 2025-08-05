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
        Schema::create('liguilla_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liguilla_id')->constrained('liguillas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('cuentas')->onDelete('cascade');
            $table->string('nombre_equipo')->nullable();
            $table->integer('puesto')->nullable();
            $table->integer('puntos')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liguilla_usuario');
    }
};
