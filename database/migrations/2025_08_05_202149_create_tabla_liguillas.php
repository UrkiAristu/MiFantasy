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
        Schema::create('liguillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->unsignedInteger('max_usuarios')->default(10);
            $table->string('codigo_unico')->unique();
            $table->foreignId('creador_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['activa', 'cerrada'])->default('activa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liguillas');
    }
};
