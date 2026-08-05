<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_menu_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_menu', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('menu_id')
                ->constrained('menu_m')
                ->cascadeOnDelete();
                
            $table->boolean('is_granted')->default(true);

            $table->timestamps();

            $table->unique(['user_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_menu');
    }
};