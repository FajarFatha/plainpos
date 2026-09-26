<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {   
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->unique(['username', 'deleted_at']);
        });
        
        Schema::table('menu_m', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['name', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username', 'deleted_at']);
            $table->unique('username');
        });

        Schema::table('menu_m', function (Blueprint $table) {
            $table->dropUnique(['name', 'deleted_at']);
            $table->unique('name');
        });
    }
};