<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scraper_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('snapshot_key')->unique();
            $table->string('path')->index();
            $table->longText('payload');
            $table->timestamp('stored_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraper_snapshots');
    }
};
