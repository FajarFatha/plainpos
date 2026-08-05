<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_m', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('head')
                ->nullable()
                ->constrained('menu_m')
                ->nullOnDelete();

            $table->string('realname');
            $table->string('description')->nullable();
            $table->string('name')->unique();
            $table->string('route')->nullable();
            $table->string('icon')->nullable();

            $table->string('span1')->nullable();
            $table->string('span2')->nullable();

            $table->unsignedInteger('nourut')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_m');
    }
};