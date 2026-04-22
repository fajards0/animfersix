<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('watchlists')) {
            return;
        }

        Schema::table('watchlists', function (Blueprint $table) {
            if (! Schema::hasColumn('watchlists', 'anime_api_id')) {
                $table->string('anime_api_id')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('watchlists', 'anime_title')) {
                $table->string('anime_title')->nullable()->after('anime_api_id');
            }

            if (! Schema::hasColumn('watchlists', 'poster_path')) {
                $table->string('poster_path')->nullable()->after('anime_title');
            }

            if (! Schema::hasColumn('watchlists', 'anime_url')) {
                $table->string('anime_url')->nullable()->after('poster_path');
            }

            if (! Schema::hasColumn('watchlists', 'score')) {
                $table->decimal('score', 3, 1)->nullable()->after('anime_url');
            }

            if (! Schema::hasColumn('watchlists', 'status')) {
                $table->string('status')->nullable()->after('score');
            }

            if (! Schema::hasColumn('watchlists', 'type')) {
                $table->string('type')->nullable()->after('status');
            }

            if (! Schema::hasColumn('watchlists', 'studio')) {
                $table->string('studio')->nullable()->after('type');
            }

            if (! Schema::hasColumn('watchlists', 'year')) {
                $table->unsignedSmallInteger('year')->nullable()->after('studio');
            }

            if (! Schema::hasColumn('watchlists', 'genres')) {
                $table->json('genres')->nullable()->after('year');
            }
        });

        if (Schema::hasColumn('watchlists', 'anime_id') && Schema::hasTable('animes')) {
            DB::table('watchlists')
                ->leftJoin('animes', 'watchlists.anime_id', '=', 'animes.id')
                ->whereNull('watchlists.anime_api_id')
                ->update([
                    'watchlists.anime_api_id' => DB::raw('COALESCE(animes.slug, watchlists.anime_id)'),
                    'watchlists.anime_title' => DB::raw('animes.title'),
                    'watchlists.poster_path' => DB::raw('animes.poster_path'),
                    'watchlists.anime_url' => DB::raw('animes.slug'),
                    'watchlists.score' => DB::raw('animes.score'),
                    'watchlists.status' => DB::raw('animes.status'),
                    'watchlists.type' => DB::raw('animes.type'),
                    'watchlists.studio' => DB::raw('animes.studio'),
                    'watchlists.year' => DB::raw('animes.year'),
                ]);
        }

        $hasUniqueIndex = collect(DB::select('SHOW INDEX FROM watchlists'))
            ->contains(fn ($index) => ($index->Key_name ?? null) === 'watchlists_user_id_anime_api_id_unique');

        if (! $hasUniqueIndex && Schema::hasColumn('watchlists', 'anime_api_id')) {
            Schema::table('watchlists', function (Blueprint $table) {
                $table->unique(['user_id', 'anime_api_id']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('watchlists')) {
            return;
        }

        $hasUniqueIndex = collect(DB::select('SHOW INDEX FROM watchlists'))
            ->contains(fn ($index) => ($index->Key_name ?? null) === 'watchlists_user_id_anime_api_id_unique');

        if ($hasUniqueIndex) {
            Schema::table('watchlists', function (Blueprint $table) {
                $table->dropUnique('watchlists_user_id_anime_api_id_unique');
            });
        }
    }
};
