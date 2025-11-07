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
         Schema::table('users', function (Blueprint $table) {
            // Añade los campos solo si no existen
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'admin')) {
                $table->boolean('admin')->default(false)->after('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'active')) {
                $table->dropColumn('active');
            }
            if (Schema::hasColumn('users', 'admin')) {
                $table->dropColumn('admin');
            }
        });
    }
};
