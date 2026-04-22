<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('anime_api_id');
            $table->string('anime_title');
            $table->string('poster_path')->nullable();
            $table->string('anime_url');
            $table->decimal('score', 3, 1)->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->string('studio')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->json('genres')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'anime_api_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
