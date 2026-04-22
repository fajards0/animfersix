<?php

namespace Database\Seeders;

use App\Models\Anime;
use App\Models\Banner;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AnimeStreamSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@animestream.test'],
            [
                'name' => 'AnimeStream Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $member = User::query()->updateOrCreate(
            ['email' => 'user@animestream.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $genres = collect([
            ['name' => 'Action', 'description' => 'Pertarungan intens dan ritme cepat.'],
            ['name' => 'Adventure', 'description' => 'Petualangan lintas dunia dan misteri.'],
            ['name' => 'Fantasy', 'description' => 'Dunia penuh sihir, monster, dan legenda.'],
            ['name' => 'Sci-Fi', 'description' => 'Teknologi futuristik dan konflik antarbintang.'],
            ['name' => 'Drama', 'description' => 'Konflik emosional dan karakter yang kuat.'],
            ['name' => 'Romance', 'description' => 'Hubungan, chemistry, dan rasa.'],
        ])->mapWithKeys(function (array $genre) {
            $model = Genre::query()->updateOrCreate(
                ['slug' => Str::slug($genre['name'])],
                $genre + ['slug' => Str::slug($genre['name'])]
            );

            return [$model->name => $model];
        });

        $animes = collect([
            [
                'title' => 'Chrono Blade',
                'studio' => 'Nova Frame',
                'year' => 2026,
                'status' => 'ongoing',
                'rating' => 'PG-13',
                'score' => 9.1,
                'type' => 'TV',
                'views' => 128000,
                'is_trending' => true,
                'is_popular' => true,
                'poster_path' => 'https://placehold.co/600x900/0f172a/f97316?text=Chrono+Blade',
                'banner_path' => 'https://placehold.co/1400x700/111827/f43f5e?text=Chrono+Blade',
                'synopsis' => 'Seorang pendekar waktu melompati timeline yang rusak untuk mencegah perang antar dimensi.',
                'genres' => ['Action', 'Fantasy', 'Adventure'],
            ],
            [
                'title' => 'Neon Ronin',
                'studio' => 'Zero District',
                'year' => 2025,
                'status' => 'completed',
                'rating' => 'R-17',
                'score' => 8.8,
                'type' => 'TV',
                'views' => 98000,
                'is_trending' => true,
                'is_popular' => true,
                'poster_path' => 'https://placehold.co/600x900/09090b/38bdf8?text=Neon+Ronin',
                'banner_path' => 'https://placehold.co/1400x700/020617/38bdf8?text=Neon+Ronin',
                'synopsis' => 'Samurai jalanan memburu sindikat cyber yang menguasai kota bawah tanah Neo Jakarta.',
                'genres' => ['Action', 'Sci-Fi', 'Drama'],
            ],
            [
                'title' => 'Eclipse Requiem',
                'studio' => 'Moon Thread',
                'year' => 2024,
                'status' => 'completed',
                'rating' => 'PG-13',
                'score' => 8.6,
                'type' => 'TV',
                'views' => 76000,
                'is_trending' => false,
                'is_popular' => true,
                'poster_path' => 'https://placehold.co/600x900/1e1b4b/e879f9?text=Eclipse+Requiem',
                'banner_path' => 'https://placehold.co/1400x700/1f2937/e879f9?text=Eclipse+Requiem',
                'synopsis' => 'Dua saudara penyihir mencoba memulihkan bulan retak yang membuat monster turun ke bumi.',
                'genres' => ['Fantasy', 'Drama', 'Adventure'],
            ],
            [
                'title' => 'Starlit Circuit',
                'studio' => 'Afterburn',
                'year' => 2026,
                'status' => 'ongoing',
                'rating' => 'PG',
                'score' => 8.3,
                'type' => 'TV',
                'views' => 51000,
                'is_trending' => true,
                'is_popular' => false,
                'poster_path' => 'https://placehold.co/600x900/0f172a/22c55e?text=Starlit+Circuit',
                'banner_path' => 'https://placehold.co/1400x700/052e16/22c55e?text=Starlit+Circuit',
                'synopsis' => 'Balapan mecha antargalaksi jadi satu-satunya cara untuk menyelamatkan koloni terakhir manusia.',
                'genres' => ['Sci-Fi', 'Adventure', 'Action'],
            ],
            [
                'title' => 'Velvet Petals',
                'studio' => 'Hanami Works',
                'year' => 2025,
                'status' => 'completed',
                'rating' => 'PG',
                'score' => 8.1,
                'type' => 'TV',
                'views' => 34000,
                'is_trending' => false,
                'is_popular' => false,
                'poster_path' => 'https://placehold.co/600x900/3f1d2e/fda4af?text=Velvet+Petals',
                'banner_path' => 'https://placehold.co/1400x700/4c1d95/fda4af?text=Velvet+Petals',
                'synopsis' => 'Kisah romantis di akademi seni elit ketika dua rival menemukan rahasia lukisan yang hidup.',
                'genres' => ['Romance', 'Drama', 'Fantasy'],
            ],
            [
                'title' => 'Iron Hearts Brigade',
                'studio' => 'Crater Lab',
                'year' => 2023,
                'status' => 'completed',
                'rating' => 'PG-13',
                'score' => 8.4,
                'type' => 'TV',
                'views' => 61000,
                'is_trending' => false,
                'is_popular' => true,
                'poster_path' => 'https://placehold.co/600x900/1f2937/facc15?text=Iron+Hearts+Brigade',
                'banner_path' => 'https://placehold.co/1400x700/111827/facc15?text=Iron+Hearts+Brigade',
                'synopsis' => 'Pasukan elit pilot armor memimpin perlawanan terakhir melawan invasi mesin kuno.',
                'genres' => ['Action', 'Sci-Fi', 'Drama'],
            ],
        ])->mapWithKeys(function (array $animeData) use ($genres) {
            $genreNames = $animeData['genres'];
            unset($animeData['genres']);

            $anime = Anime::query()->updateOrCreate(
                ['slug' => Str::slug($animeData['title'])],
                $animeData + ['slug' => Str::slug($animeData['title'])]
            );

            $anime->genres()->sync(collect($genreNames)->map(fn (string $name) => $genres[$name]->id));

            return [$anime->title => $anime];
        });

        foreach ($animes as $anime) {
            foreach (range(1, 3) as $number) {
                Episode::query()->updateOrCreate(
                    ['slug' => Str::slug($anime->title . '-episode-' . $number)],
                    [
                        'anime_id' => $anime->id,
                        'title' => $anime->title . ' Episode ' . $number,
                        'slug' => Str::slug($anime->title . '-episode-' . $number),
                        'episode_number' => $number,
                        'synopsis' => 'Episode ' . $number . ' menghadirkan eskalasi konflik dan momen karakter penting.',
                        'video_url' => $number % 2 === 0
                            ? 'https://samplelib.com/lib/preview/mp4/sample-10s.mp4'
                            : 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4',
                        'duration_minutes' => 24,
                        'aired_at' => now()->subDays((4 - $number) * 3),
                        'is_published' => true,
                    ]
                );
            }
        }

        Banner::query()->updateOrCreate(
            ['title' => 'Streaming Legal, Tampilan Sinematik'],
            [
                'subtitle' => 'Tema dark anime modern dengan katalog responsif, pencarian cepat, dan placeholder video legal.',
                'image_path' => 'https://placehold.co/1400x700/0f172a/f97316?text=AnimeStream+Hero',
                'button_text' => 'Jelajahi Anime',
                'button_link' => route('anime.index', [], false),
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Banner::query()->updateOrCreate(
            ['title' => 'Admin Dashboard Siap Kelola Konten'],
            [
                'subtitle' => 'CRUD anime, episode, genre, banner, dan user sudah tersedia untuk workflow tim Anda.',
                'image_path' => 'https://placehold.co/1400x700/111827/38bdf8?text=Admin+Dashboard',
                'button_text' => 'Masuk Admin',
                'button_link' => route('admin.dashboard', [], false),
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        unset($member, $admin);
    }
}
