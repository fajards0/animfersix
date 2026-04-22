<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('synopsis');
            $table->string('poster_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('studio')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('status')->default('ongoing');
            $table->string('rating')->default('PG-13');
            $table->decimal('score', 3, 1)->default(8.0);
            $table->string('type')->default('TV');
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
