<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_menu', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                ->constrained('groups_m')
                ->cascadeOnDelete();

            $table->foreignId('menu_id')
                ->constrained('menu_m')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['group_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_menu');
    }
};