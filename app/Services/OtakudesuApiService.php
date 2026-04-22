<?php

namespace App\Services;

use App\Models\ScraperSnapshot;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Throwable;

class OtakudesuApiService
{
    public function homeData(): Fluent
    {
        $payload = $this->getCached('home', 'home');
        $home = data_get($payload, 'home', []);

        $ongoing = collect(data_get($home, 'on_going', []))
            ->map(fn (array $item) => $this->normalizeAnimeSummary($item, 'ongoing'))
            ->map(fn (Fluent $anime) => $this->enrichAnimeImages($anime));

        $complete = collect(data_get($home, 'complete', []))
            ->map(fn (array $item) => $this->normalizeAnimeSummary($item, 'completed'))
            ->map(fn (Fluent $anime) => $this->enrichAnimeImages($anime));

        if ($ongoing->isEmpty()) {
            $ongoing = collect(data_get($this->getCached('ongoing:1', 'ongoing/page/1'), 'animeList', []))
                ->map(fn (array $item) => $this->normalizeAnimeSummary($item, 'ongoing'))
                ->map(fn (Fluent $anime) => $this->enrichAnimeImages($anime))
                ->values();
        }

        if ($complete->isEmpty()) {
            $complete = collect(data_get($this->getCached('complete:1', 'complete/page/1'), 'animeList', []))
                ->map(fn (array $item) => $this->normalizeAnimeSummary($item, 'completed'))
                ->map(fn (Fluent $anime) => $this->enrichAnimeImages($anime))
                ->values();
        }

        return new Fluent([
            'trending' => $ongoing->take(6),
            'popular' => $complete->sortByDesc('score')->take(8)->values(),
            'latestEpisodes' => $this->latestEpisodesFromOngoing($ongoing->take(6)),
            'ongoing' => $ongoing,
            'complete' => $complete,
        ]);
    }

    public function homeApi(): array
    {
        $payload = $this->getCached('home', 'home');

        return [
            'status' => data_get($payload, 'status', 'success'),
            'baseUrl' => data_get($payload, 'baseUrl', 'https://otakudesu.moe/'),
            'home' => [
                'on_going' => array_values(data_get($payload, 'home.on_going', [])),
                'complete' => array_values(data_get($payload, 'home.complete', [])),
            ],
        ];
    }

    public function getGenres(): Collection
    {
        $payload = $this->getCached('genres', 'genres');

        return collect(data_get($payload, 'genreList', []))
            ->map(fn (array $item) => new Fluent([
                'id' => $this->sanitizeId((string) data_get($item, 'id', '')),
                'name' => data_get($item, 'genre_name', 'Unknown'),
                'image' => data_get($item, 'image_link'),
            ]));
    }

    public function catalog(array $filters, int $page = 1): Fluent
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $genre = trim((string) ($filters['genre'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));
        $year = trim((string) ($filters['year'] ?? ''));
        $minScore = trim((string) ($filters['rating'] ?? ''));
        $source = 'all';
        $rawItems = collect();

        if ($search !== '') {
            $source = 'search';
            $payload = $this->getCached('search:' . md5($search), 'search/' . rawurlencode($search));
            $rawItems = collect(data_get($payload, 'search_results', []));
        } elseif ($genre !== '') {
            $source = $this->shouldUseFastGenrePath($year, $minScore) ? 'genre-fast' : 'genre-all';
            $payload = $source === 'genre-fast'
                ? $this->getCached("genre-catalog:{$genre}:page:{$page}", "genres/{$genre}/catalog/page/{$page}")
                : $this->getCached("genre-catalog:{$genre}", "genres/{$genre}/catalog");
            $rawItems = collect(data_get($payload, 'animeList', []));
        } elseif ($status === 'completed') {
            $source = 'completed';
            $payload = $this->getCached("complete:{$page}", "complete/page/{$page}");
            $rawItems = collect(data_get($payload, 'animeList', []));
        } elseif ($status === 'ongoing') {
            $source = 'ongoing';
            $payload = $this->getCached("ongoing:{$page}", "ongoing/page/{$page}");
            $rawItems = collect(data_get($payload, 'animeList', []));
        } else {
            $payload = $this->getCached('anime-list', 'anime-list');
            $rawItems = collect(data_get($payload, 'animeList', []));
        }

        $items = $rawItems
            ->map(fn (array $item) => $this->normalizeAnimeSummary($item, data_get($item, 'status', $status)))
            ->filter(function (Fluent $anime) use ($year, $minScore) {
                if ($year !== '' && (string) $anime->year !== $year) {
                    return false;
                }

                if ($minScore !== '' && (float) $anime->score < (float) $minScore) {
                    return false;
                }

                return true;
            })
            ->values();

        $perPage = $this->catalogPerPage($source);
        $pageItems = in_array($source, ['all', 'genre-all'], true)
            ? $items->forPage($page, $perPage)->values()
            : $items;
        if ($source === 'genre-fast') {
            $pageItems = $items->take($perPage)->values();
        }
        $pageItems = $pageItems
            ->map(fn (Fluent $anime) => $this->enrichAnimeImages($anime, $source !== 'genre-fast'))
            ->values();
        $paginator = new LengthAwarePaginator(
            $pageItems,
            in_array($source, ['all', 'genre-all'], true)
                ? $items->count()
                : ((($page - 1) * $perPage) + $items->count() + ($this->sourceHasMore($source, $rawItems->count()) ? 1 : 0)),
            $perPage,
            $page,
            [
                'path' => route('anime.index'),
                'query' => request()->query(),
            ]
        );

        return new Fluent([
            'items' => collect($paginator->items()),
            'pagination' => new Fluent([
                'paginator' => $paginator,
                'current_page' => $page,
                'previous_page' => $page > 1 ? $page - 1 : null,
                'next_page' => in_array($source, ['all', 'genre-all'], true)
                    ? ($paginator->hasMorePages() ? $page + 1 : null)
                    : ($this->sourceHasMore($source, $rawItems->count()) ? $page + 1 : null),
            ]),
        ]);
    }

    public function animeDetail(string $animeId): Fluent
    {
        $animeId = $this->sanitizeId($animeId);
        $payload = $this->getCached('anime:' . md5($animeId), 'anime/' . $animeId);

        $episodes = collect(data_get($payload, 'episode_list', []))
            ->reject(fn (array $item) => str_contains(Str::lower((string) data_get($item, 'title', '')), 'masih kosong'))
            ->map(fn (array $item) => $this->normalizeEpisodeSummary($item, $animeId))
            ->values();

        return new Fluent([
            'id' => $animeId,
            'route_id' => $this->encodeId($animeId),
            'title' => data_get($payload, 'title', 'Unknown Anime'),
            'poster_image' => $this->posterImage(data_get($payload, 'thumb')),
            'banner_image' => $this->bannerImage(data_get($payload, 'thumb')),
            'synopsis' => data_get($payload, 'synopsis', 'Synopsis belum tersedia.'),
            'japanese' => data_get($payload, 'japanase'),
            'score' => (float) data_get($payload, 'score', 0),
            'producer' => data_get($payload, 'producer'),
            'type' => data_get($payload, 'type'),
            'status' => data_get($payload, 'status'),
            'total_episode' => data_get($payload, 'total_episode'),
            'duration' => data_get($payload, 'duration'),
            'release_date' => data_get($payload, 'release_date'),
            'year' => $this->extractYear((string) data_get($payload, 'release_date')),
            'studio' => data_get($payload, 'studio'),
            'views' => 0,
            'genres' => collect(data_get($payload, 'genre_list', []))
                ->map(fn (array $genre) => new Fluent([
                    'id' => $this->sanitizeId((string) data_get($genre, 'genre_id', '')),
                    'name' => data_get($genre, 'genre_name', 'Unknown'),
                ]))
                ->values(),
            'episodes' => $episodes,
        ]);
    }

    public function completeAnimeApi(int $page = 1): array
    {
        $page = max(1, $page);
        $payload = $this->getCached("complete:{$page}", "complete/page/{$page}");

        return [
            'status' => data_get($payload, 'status', 'success'),
            'baseUrl' => data_get($payload, 'baseUrl', 'https://otakudesu.moe/complete-anime/'),
            'animeList' => array_values(data_get($payload, 'animeList', [])),
        ];
    }

    public function episodeDetail(string $episodeId, string $animeId, ?string $preferredQuality = null, ?Fluent $anime = null): Fluent
    {
        $episodeId = $this->sanitizeId($episodeId);
        $payload = $this->getCached('episode:' . md5($episodeId), 'eps/' . $episodeId);
        $anime = $anime ?: $this->animeDetail($animeId);
        $qualities = collect(data_get($payload, 'qualities', []));
        $streamOptions = collect(data_get($payload, 'stream_options', []));

        if ($qualities->isEmpty()) {
            $qualities = collect([
                data_get($payload, 'quality.low_quality'),
                data_get($payload, 'quality.medium_quality'),
                data_get($payload, 'quality.high_quality'),
            ])->filter()->values();
        }

        $selectedStream = $this->selectStreamOption($streamOptions, $preferredQuality);

        return new Fluent([
            'id' => $episodeId,
            'route_id' => $this->encodeId($episodeId),
            'title' => data_get($payload, 'title', 'Episode'),
            'stream_url' => data_get($selectedStream, 'url', data_get($payload, 'link_stream', 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4')),
            'stream_quality' => data_get($selectedStream, 'quality', data_get($payload, 'stream_quality')),
            'stream_label' => data_get($selectedStream, 'label'),
            'stream_options' => $streamOptions,
            'qualities' => $qualities,
            'mirror_groups' => collect([
                data_get($payload, 'mirror1'),
                data_get($payload, 'mirror2'),
                data_get($payload, 'mirror3'),
            ])->filter()->values(),
            'anime' => $anime,
            'episode_number' => $this->extractEpisodeNumber((string) data_get($payload, 'title', '')),
            'synopsis' => 'Episode diputar dari link streaming yang disediakan API Otakudesu lokal.',
        ]);
    }

    public function relatedAnime(Fluent $anime, int $limit = 4): Collection
    {
        $firstGenre = $anime->genres->first();

        if (! $firstGenre) {
            return $this->homeData()->popular->take($limit);
        }

        $payload = $this->getCached("genre:{$firstGenre->id}:1", "genres/{$firstGenre->id}/page/1");

        return collect(data_get($payload, 'animeList', []))
            ->map(fn (array $item) => $this->normalizeAnimeSummary($item))
            ->map(fn (Fluent $item) => $this->enrichAnimeImages($item))
            ->reject(fn (Fluent $item) => $item->id === $anime->id)
            ->take($limit)
            ->values();
    }

    public function scoreOptions(): array
    {
        return [
            '9' => '9.0+',
            '8' => '8.0+',
            '7' => '7.0+',
        ];
    }

    public function yearOptions(): Collection
    {
        $currentYear = (int) now()->format('Y');

        return collect(range($currentYear, $currentYear - 10));
    }

    public function scraperHealth(): array
    {
        $apiUrl = $this->apiBaseUrl();
        $sourceUrl = rtrim((string) config('services.otakudesu.source_url', 'https://otakudesu.best'), '/');

        return [
            'configured' => [
                'api_base_url' => $apiUrl,
                'source_url' => $sourceUrl,
                'proxy_disabled' => (bool) config('services.otakudesu.disable_proxy', true),
            ],
            'checks' => [
                'api' => $apiUrl !== ''
                    ? $this->probe(fn () => $this->client()->get('home'))
                    : [
                        'ok' => false,
                        'status' => null,
                        'message' => 'disabled',
                    ],
                'source' => $this->probe(fn () => $this->sourceClient()->get('/')),
            ],
            'cache' => [
                'unavailable' => (bool) Cache::get('otakudesu:unavailable', false),
                'last_error' => Cache::get('otakudesu:last_error'),
            ],
        ];
    }

    public function encodeId(string $id): string
    {
        return rawurlencode($this->sanitizeId($id));
    }

    public function decodeId(string $id): string
    {
        return $this->sanitizeId(rawurldecode($id));
    }

    private function latestEpisodesFromOngoing(Collection $ongoing): Collection
    {
        return $ongoing
            ->map(function (Fluent $anime) {
                $detail = $this->animeDetail($anime->id);
                $episode = $detail->episodes->first();

                if (! $episode) {
                    return null;
                }

                return new Fluent([
                    'title' => $episode->title,
                    'episode_number' => $episode->episode_number,
                    'uploaded_on' => $episode->uploaded_on,
                    'route_id' => $episode->route_id,
                    'anime_route_id' => $anime->route_id,
                    'anime' => $anime,
                    'synopsis' => 'Episode terbaru dari ' . $anime->title . '.',
                ]);
            })
            ->filter()
            ->values();
    }

    private function normalizeAnimeSummary(array $item, ?string $fallbackStatus = null): Fluent
    {
        $id = $this->sanitizeId((string) data_get($item, 'id', ''));

        return new Fluent([
            'id' => $id,
            'route_id' => $this->encodeId($id),
            'title' => data_get($item, 'title') ?? data_get($item, 'anime_name') ?? 'Unknown Anime',
            'poster_image' => $this->posterImage(data_get($item, 'thumb')),
            'banner_image' => $this->bannerImage(data_get($item, 'thumb')),
            'synopsis' => data_get($item, 'episode', 'Detail anime tersedia di halaman detail.'),
            'studio' => data_get($item, 'studio'),
            'year' => $this->extractYear((string) (data_get($item, 'release_date') ?? data_get($item, 'uploaded_on', ''))),
            'status' => $this->normalizeStatus(data_get($item, 'status', $fallbackStatus)),
            'score' => (float) data_get($item, 'score', 0),
            'type' => data_get($item, 'day_updated', 'Anime'),
            'views' => 0,
            'genres' => collect(data_get($item, 'genre_list', []))
                ->map(fn (array $genre) => new Fluent([
                    'id' => $this->sanitizeId((string) (data_get($genre, 'genre_id') ?? data_get($genre, 'id', ''))),
                    'name' => data_get($genre, 'genre_name') ?? data_get($genre, 'genre_title') ?? 'Unknown',
                ]))
                ->values(),
            'episode_label' => data_get($item, 'episode'),
            'uploaded_on' => data_get($item, 'uploaded_on'),
            'release_date' => data_get($item, 'release_date'),
        ]);
    }

    private function normalizeEpisodeSummary(array $item, string $animeId): Fluent
    {
        $id = $this->sanitizeId((string) data_get($item, 'id', ''));
        $title = (string) data_get($item, 'title', 'Episode');

        return new Fluent([
            'id' => $id,
            'route_id' => $this->encodeId($id),
            'anime_id' => $animeId,
            'anime_route_id' => $this->encodeId($animeId),
            'title' => $title,
            'episode_number' => $this->extractEpisodeNumber($title),
            'uploaded_on' => data_get($item, 'uploaded_on'),
            'synopsis' => 'Episode tersedia melalui API Otakudesu lokal.',
        ]);
    }

    private function enrichAnimeImages(Fluent $anime, bool $hydrateScore = true): Fluent
    {
        if (
            ! $this->isFallbackImage((string) $anime->poster_image)
            && ! $this->isFallbackImage((string) $anime->banner_image)
            && (! $hydrateScore || (float) $anime->score > 0)
        ) {
            return $anime;
        }

        try {
            $detail = $this->animeDetail((string) $anime->id);
        } catch (Throwable) {
            return $anime;
        }

        $anime->poster_image = $this->isFallbackImage((string) $anime->poster_image)
            ? $detail->poster_image
            : $anime->poster_image;

        $anime->banner_image = $this->isFallbackImage((string) $anime->banner_image)
            ? $detail->banner_image
            : $anime->banner_image;

        if (empty($anime->genres) || collect($anime->genres)->isEmpty()) {
            $anime->genres = $detail->genres;
        }

        if (($anime->studio === null || $anime->studio === '') && $detail->studio) {
            $anime->studio = $detail->studio;
        }

        if ((int) $anime->year === 0 && $detail->year) {
            $anime->year = $detail->year;
        }

        if ($hydrateScore && (float) $anime->score === 0.0 && $detail->score) {
            $anime->score = $detail->score;
        }

        return $anime;
    }

    private function normalizeStatus(?string $status): string
    {
        $value = Str::lower((string) $status);

        return match (true) {
            str_contains($value, 'ongoing') => 'ongoing',
            str_contains($value, 'complete') || str_contains($value, 'completed') => 'completed',
            default => $status ?: 'unknown',
        };
    }

    private function extractYear(string $value): ?int
    {
        if (preg_match('/(19|20)\d{2}/', $value, $matches)) {
            return (int) $matches[0];
        }

        return null;
    }

    private function extractEpisodeNumber(string $title): int
    {
        if (preg_match('/episode\s+(\d+)/i', $title, $matches)) {
            return (int) $matches[1];
        }

        return 1;
    }

    private function sourceHasMore(string $source, int $rawCount): bool
    {
        if ($source === 'search') {
            return false;
        }

        return $rawCount >= 10;
    }

    private function sanitizeId(string $id): string
    {
        return trim($id, '/');
    }

    private function getCached(string $key, string $path): array
    {
        return Cache::remember('otakudesu:v4:' . $key, now()->addMinutes(10), function () use ($path) {
            if ($this->hasApiBaseUrl() && ! Cache::get('otakudesu:api_unavailable')) {
                try {
                    $response = $this->get($path);
                    $this->storeSnapshot($path, $response);
                    Cache::forget('otakudesu:api_unavailable');
                    Cache::forget('otakudesu:unavailable');
                    Cache::forget('otakudesu:last_error');

                    return $response;
                } catch (Throwable $exception) {
                    Cache::put('otakudesu:api_unavailable', true, now()->addMinutes(5));
                    Cache::put('otakudesu:last_error', [
                        'path' => $path,
                        'message' => $exception->getMessage(),
                        'time' => now()->toIso8601String(),
                    ], now()->addMinutes(10));
                    Log::warning('Otakudesu upstream request failed, using scraper fallback.', [
                        'path' => $path,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            try {
                $fallback = $this->scrapeFallback($path);
            } catch (Throwable $exception) {
                Cache::put('otakudesu:last_error', [
                    'path' => $path,
                    'message' => $exception->getMessage(),
                    'time' => now()->toIso8601String(),
                ], now()->addMinutes(10));
                Log::warning('Otakudesu scraper fallback failed.', [
                    'path' => $path,
                    'message' => $exception->getMessage(),
                ]);
                $fallback = [];
            }

            if ($fallback !== []) {
                $this->storeSnapshot($path, $fallback);
                Cache::forget('otakudesu:unavailable');

                return $fallback;
            }

            $snapshot = $this->getSnapshot($path);

            if ($snapshot !== []) {
                Log::info('Serving Otakudesu payload from local snapshot.', [
                    'path' => $path,
                ]);

                return $snapshot;
            }

            Cache::put('otakudesu:unavailable', true, now()->addMinutes(1));

            return [];
        });
    }

    public function refreshSnapshots(array $paths): array
    {
        $results = [];

        foreach ($paths as $path) {
            $path = trim((string) $path);

            if ($path === '') {
                continue;
            }

            try {
                try {
                    $payload = $this->get($path);
                } catch (Throwable) {
                    $payload = $this->scrapeFallback($path);
                }

                if ($payload === []) {
                    $results[$path] = 'empty';
                    continue;
                }

                $this->storeSnapshot($path, $payload);
                Cache::put('otakudesu:v4:' . $this->cacheKeyForPath($path), $payload, now()->addMinutes(10));
                $results[$path] = 'ok';
            } catch (Throwable $exception) {
                $results[$path] = 'failed: ' . $exception->getMessage();
            }
        }

        return $results;
    }

    public function warmSnapshotPaths(bool $deep = false): array
    {
        $pages = max(1, (int) config('services.otakudesu.warm_pages', 2));
        $genres = collect(config('services.otakudesu.warm_genres', []))
            ->map(fn ($genre) => trim((string) $genre))
            ->filter()
            ->unique()
            ->values();

        $paths = collect([
            'home',
            'genres',
            'anime-list',
        ]);

        foreach (range(1, $pages) as $page) {
            $paths->push("ongoing/page/{$page}");
            $paths->push("complete/page/{$page}");
        }

        foreach ($genres as $genre) {
            $paths->push("genres/{$genre}/catalog/page/1");

            if ($deep) {
                foreach (range(2, $pages) as $page) {
                    $paths->push("genres/{$genre}/catalog/page/{$page}");
                }
            }
        }

        if ($deep) {
            foreach ($this->warmAnimeIds() as $animeId) {
                $paths->push("anime/{$animeId}");
            }
        }

        return $paths->unique()->values()->all();
    }

    private function scrapeFallback(string $path): array
    {
        if ($path === 'home') {
            return $this->scrapeHomePayload();
        }

        if ($path === 'anime-list') {
            return $this->scrapeAnimeListPayload();
        }

        if ($path === 'genres') {
            return $this->scrapeGenreListPayload();
        }

        if (preg_match('#^complete/page/(\d+)$#', $path, $matches)) {
            return $this->scrapeArchivePayload('complete-anime', 'completed', (int) $matches[1]);
        }

        if (preg_match('#^ongoing/page/(\d+)$#', $path, $matches)) {
            return $this->scrapeArchivePayload('ongoing-anime', 'ongoing', (int) $matches[1]);
        }

        if (preg_match('#^genres/([^/]+)/page/(\d+)$#', $path, $matches)) {
            return $this->scrapeGenrePagePayload($matches[1], (int) $matches[2]);
        }

        if (preg_match('#^genres/([^/]+)/catalog/page/(\d+)$#', $path, $matches)) {
            return $this->scrapeFastGenreCatalogPayload($matches[1], (int) $matches[2]);
        }

        if (preg_match('#^genres/([^/]+)/catalog$#', $path, $matches)) {
            return $this->scrapeGenreCatalogPayload($matches[1]);
        }

        if (preg_match('#^search/(.+)$#', $path, $matches)) {
            return $this->scrapeSearchPayload(rawurldecode($matches[1]));
        }

        if (preg_match('#^anime/(.+)$#', $path, $matches)) {
            return $this->scrapeAnimePayload($matches[1]);
        }

        if (preg_match('#^eps/(.+)$#', $path, $matches)) {
            return $this->scrapeEpisodePayload($matches[1]);
        }

        return [];
    }

    private function scrapeHomePayload(): array
    {
        $xpath = $this->sourceXPath('/');
        $sections = $xpath->query("//div[contains(@class,'rseries')]");

        $ongoing = [];
        $complete = [];

        foreach ($sections as $section) {
            $heading = Str::lower($this->queryText($xpath, ".//h2|.//h4|.//div[contains(@class,'thetitle')]", $section) ?? '');
            $items = $this->parsePosterListItems($xpath, ".//li", $section);

            if (str_contains($heading, 'on-going') || str_contains($heading, 'ongoing')) {
                $ongoing = $items;
            }

            if (str_contains($heading, 'complete') || str_contains($heading, 'selesai')) {
                $complete = $items;
            }
        }

        if ($ongoing === [] || $complete === []) {
            $all = $this->parsePosterListItems($xpath, "(//div[contains(@class,'venz')])[1]//li");
            $ongoing = $ongoing ?: $this->scrapeArchivePayload('ongoing-anime', 'ongoing', 1)['animeList'];
            $ongoing = $ongoing ?: array_slice($all, 0, 15);
            $complete = $complete ?: $this->scrapeArchivePayload('complete-anime', 'completed', 1)['animeList'];
        }

        return [
            'status' => 'success',
            'baseUrl' => 'https://otakudesu.moe/',
            'home' => [
                'on_going' => array_values($ongoing),
                'complete' => array_values($complete),
            ],
        ];
    }

    private function scrapeArchivePayload(string $segment, string $status, int $page): array
    {
        $page = max(1, $page);
        $path = '/' . trim($segment, '/') . '/';

        if ($page > 1) {
            $path .= 'page/' . $page . '/';
        }

        $xpath = $this->sourceXPath($path);
        $items = $this->parsePosterListItems($xpath, "//div[contains(@class,'venz')]//ul/li");

        foreach ($items as &$item) {
            $item['status'] = $status;
        }

        return [
            'status' => 'success',
            'baseUrl' => 'https://otakudesu.moe/' . trim($segment, '/') . '/',
            'animeList' => array_values($items),
        ];
    }

    private function scrapeGenreListPayload(): array
    {
        $xpath = $this->sourceXPath('/genre-list/');
        $genres = [];

        foreach ($xpath->query("//a[contains(@href,'/genres/')]") as $genreLink) {
            $name = $this->cleanText($genreLink->textContent);
            $href = $genreLink instanceof \DOMElement ? $genreLink->getAttribute('href') : '';
            $id = $this->extractSiteId($href, 'genres');

            if ($name === '' || $id === '') {
                continue;
            }

            $genres[$id] = [
                'id' => $id,
                'genre_name' => $name,
                'image_link' => null,
            ];
        }

        return [
            'genreList' => array_values($genres),
        ];
    }

    private function scrapeAnimeListPayload(): array
    {
        $xpath = $this->sourceXPath('/anime-list/');
        $items = [];

        foreach ($xpath->query("//a[contains(@href,'/anime/')]") as $link) {
            if (! $link instanceof \DOMElement) {
                continue;
            }

            $href = $link->getAttribute('href');
            $id = $this->extractSiteId($href, 'anime');
            $title = $this->cleanText($link->textContent);

            if ($id === '' || $title === '') {
                continue;
            }

            $status = str_contains(Str::lower($title), 'on-going') ? 'ongoing' : 'completed';
            $cleanTitle = trim((string) Str::of($title)->replace('On-Going', ''));

            $items[$id] = [
                'id' => $id,
                'title' => $cleanTitle,
                'status' => $status,
                'thumb' => null,
                'episode' => null,
                'score' => 0,
                'uploaded_on' => null,
                'release_date' => null,
            ];
        }

        return [
            'status' => 'success',
            'baseUrl' => 'https://otakudesu.moe/anime-list/',
            'animeList' => array_values($items),
        ];
    }

    private function scrapeGenrePagePayload(string $genreId, int $page): array
    {
        $page = max(1, $page);
        $path = '/genres/' . trim($genreId, '/') . '/';

        if ($page > 1) {
            $path .= 'page/' . $page . '/';
        }

        $xpath = $this->sourceXPath($path);
        $items = [];

        foreach ($xpath->query("//div[contains(@class,'col-anime-con')]//div[contains(@class,'col-anime')]") as $node) {
            $titleLink = $this->queryAttr($xpath, ".//div[contains(@class,'col-anime-title')]/a", 'href', $node);
            $id = $this->extractSiteId($titleLink, 'anime');

            if ($id === '') {
                continue;
            }

            $items[] = [
                'id' => $id,
                'title' => $this->queryText($xpath, ".//div[contains(@class,'col-anime-title')]/a", $node),
                'thumb' => $this->queryAttr($xpath, ".//div[contains(@class,'col-anime-cover')]//img", 'src', $node),
                'studio' => $this->queryText($xpath, ".//div[contains(@class,'col-anime-studio')]", $node),
                'episode' => $this->queryText($xpath, ".//div[contains(@class,'col-anime-eps')]", $node),
                'score' => (float) ($this->queryText($xpath, ".//div[contains(@class,'col-anime-rating')]", $node) ?: 0),
                'release_date' => $this->queryText($xpath, ".//div[contains(@class,'col-anime-date')]", $node),
                'status' => $this->inferStatusFromEpisodeLabel($this->queryText($xpath, ".//div[contains(@class,'col-anime-eps')]", $node)),
                'genre_list' => $this->parseGenreAnchors($xpath, ".//div[contains(@class,'col-anime-genre')]//a", $node),
            ];
        }

        return [
            'status' => 'success',
            'animeList' => $items,
        ];
    }

    private function scrapeGenreCatalogPayload(string $genreId): array
    {
        $genreId = trim($genreId, '/');
        $allItems = collect();
        $seenIds = [];
        $maxPages = 20;

        for ($page = 1; $page <= $maxPages; $page++) {
            $payload = $this->scrapeGenrePagePayload($genreId, $page);
            $pageItems = collect(data_get($payload, 'animeList', []));

            if ($pageItems->isEmpty()) {
                break;
            }

            $newItems = $pageItems
                ->filter(function (array $item) use (&$seenIds) {
                    $id = (string) data_get($item, 'id', '');

                    if ($id === '' || isset($seenIds[$id])) {
                        return false;
                    }

                    $seenIds[$id] = true;

                    return true;
                })
                ->values();

            if ($newItems->isEmpty()) {
                break;
            }

            $allItems = $allItems->concat($newItems);

            if ($pageItems->count() < 10) {
                break;
            }
        }

        return [
            'status' => 'success',
            'animeList' => $allItems->values()->all(),
        ];
    }

    private function scrapeFastGenreCatalogPayload(string $genreId, int $page): array
    {
        $genreId = trim($genreId, '/');
        $page = max(1, $page);
        $perPage = $this->catalogPerPage('genre-fast');
        $approxSourceItemsPerPage = 24;
        $sourcePagesPerAppPage = max(1, (int) ceil($perPage / $approxSourceItemsPerPage));
        $sourceStartPage = (($page - 1) * $sourcePagesPerAppPage) + 1;
        $requiredItems = $perPage + 1;
        $allItems = collect();
        $seenIds = [];
        $sourcePage = $sourceStartPage;
        $maxSourcePages = $sourceStartPage + $sourcePagesPerAppPage + 1;

        while ($sourcePage <= $maxSourcePages && $allItems->count() < $requiredItems) {
            $payload = $this->scrapeGenrePagePayload($genreId, $sourcePage);
            $pageItems = collect(data_get($payload, 'animeList', []));

            if ($pageItems->isEmpty()) {
                break;
            }

            $newItems = $pageItems
                ->filter(function (array $item) use (&$seenIds) {
                    $id = (string) data_get($item, 'id', '');

                    if ($id === '' || isset($seenIds[$id])) {
                        return false;
                    }

                    $seenIds[$id] = true;

                    return true;
                })
                ->values();

            if ($newItems->isEmpty()) {
                break;
            }

            $allItems = $allItems->concat($newItems);

            if ($pageItems->count() < 10) {
                break;
            }

            $sourcePage++;
        }

        return [
            'status' => 'success',
            'animeList' => $allItems->take($perPage + 1)->values()->all(),
        ];
    }

    private function scrapeSearchPayload(string $query): array
    {
        $xpath = $this->sourceXPath('/?s=' . rawurlencode($query) . '&post_type=anime');
        $items = [];

        foreach ($xpath->query("//ul[contains(@class,'chivsrc')]//li") as $node) {
            $titleLink = $this->queryAttr($xpath, ".//h2/a", 'href', $node);
            $id = $this->extractSiteId($titleLink, 'anime');

            if ($id === '') {
                continue;
            }

            $items[] = [
                'id' => $id,
                'title' => $this->queryText($xpath, ".//h2/a", $node),
                'thumb' => $this->queryAttr($xpath, ".//img", 'src', $node),
                'status' => $this->extractLabeledValue($xpath, $node, 'Status'),
                'score' => (float) ($this->extractLabeledValue($xpath, $node, 'Rating') ?: 0),
                'genre_list' => $this->parseGenreAnchors($xpath, ".//div[contains(@class,'set')]//a", $node),
            ];
        }

        return [
            'status' => 'success',
            'search_results' => $items,
        ];
    }

    private function scrapeAnimePayload(string $animeId): array
    {
        $xpath = $this->sourceXPath('/anime/' . trim($animeId, '/') . '/');
        $info = [];

        foreach ($xpath->query("//div[contains(@class,'infozingle')]//p") as $row) {
            $label = $this->cleanText($this->queryText($xpath, './/b', $row) ?? '');
            $value = $this->cleanText(str_replace($label . ':', '', $row->textContent));

            if ($label !== '') {
                $info[Str::lower($label)] = $value;
            }
        }

        $episodes = [];

        foreach ($xpath->query("//div[contains(@class,'episodelist')]//li") as $node) {
            $title = $this->queryText($xpath, ".//a", $node);
            $href = $this->queryAttr($xpath, ".//a", 'href', $node);
            $id = $this->extractSiteId($href, 'episode');

            if ($title === null || $id === '') {
                continue;
            }

            $episodes[] = [
                'id' => $id,
                'title' => $title,
                'uploaded_on' => $this->queryText($xpath, ".//span[contains(@class,'zeebr')]", $node),
            ];
        }

        return [
            'title' => $info['judul'] ?? 'Unknown Anime',
            'thumb' => $this->queryAttr($xpath, "//div[contains(@class,'fotoanime')]//img", 'src'),
            'synopsis' => $this->queryText($xpath, "//div[contains(@class,'sinopc')]"),
            'japanase' => $info['japanese'] ?? null,
            'score' => (float) ($info['skor'] ?? 0),
            'producer' => $info['produser'] ?? null,
            'type' => $info['tipe'] ?? null,
            'status' => $info['status'] ?? null,
            'total_episode' => $info['total episode'] ?? null,
            'duration' => $info['durasi'] ?? null,
            'release_date' => $info['tanggal rilis'] ?? null,
            'studio' => $info['studio'] ?? null,
            'genre_list' => $this->parseCommaGenres($info['genre'] ?? ''),
            'episode_list' => $episodes,
        ];
    }

    private function scrapeEpisodePayload(string $episodeId): array
    {
        $path = '/episode/' . trim($episodeId, '/') . '/';
        $page = $this->sourcePage($path);
        $xpath = $page['xpath'];
        $title = $this->queryText($xpath, "//div[contains(@class,'download')]//h4")
            ?? $this->queryText($xpath, "//title")
            ?? Str::of($episodeId)->replace('-', ' ')->title()->toString();
        $qualities = $this->parseDownloadQualities($xpath);
        $mirrorGroups = $this->parseMirrorGroups($xpath);
        $streamOptions = $this->resolveStreamOptions($mirrorGroups, $page['html'], $page['url']);
        $selectedStream = $this->selectStreamOption(collect($streamOptions));
        $fallbackStream = $this->queryAttr($xpath, "//iframe", 'src') ?: 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4';

        return [
            'title' => $title,
            'link_stream' => data_get($selectedStream, 'url', $fallbackStream),
            'stream_quality' => data_get($selectedStream, 'quality'),
            'stream_options' => $streamOptions,
            'qualities' => $qualities,
            'quality' => [
                'low_quality' => $qualities[0] ?? null,
                'medium_quality' => $qualities[1] ?? null,
                'high_quality' => $qualities[2] ?? null,
            ],
            'mirror1' => $mirrorGroups[0] ?? [],
            'mirror2' => $mirrorGroups[1] ?? [],
            'mirror3' => $mirrorGroups[2] ?? [],
        ];
    }

    private function get(string $path): array
    {
        return $this->client()->get($path)->throw()->json() ?? [];
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->apiBaseUrl())
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => 'Fer6origami/1.0 (+Laravel)',
            ])
            ->withOptions($this->httpOptions())
            ->connectTimeout(2)
            ->timeout(5);
    }

    private function hasApiBaseUrl(): bool
    {
        return $this->apiBaseUrl() !== '';
    }

    private function apiBaseUrl(): string
    {
        $baseUrl = trim((string) config('services.otakudesu.base_url', ''));

        if (! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            return '';
        }

        return rtrim($baseUrl, '/');
    }

    private function sourceClient(?string $baseUrl = null): PendingRequest
    {
        return Http::baseUrl(rtrim($baseUrl ?: config('services.otakudesu.source_url', 'https://otakudesu.best'), '/'))
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9,id;q=0.8',
            ])
            ->withOptions($this->httpOptions())
            ->connectTimeout(5)
            ->timeout(10);
    }

    private function httpOptions(): array
    {
        if (! config('services.otakudesu.disable_proxy', true)) {
            return [];
        }

        return [
            'proxy' => '',
            'verify' => false,
        ];
    }

    private function sourceXPath(string $path): \DOMXPath
    {
        return $this->sourcePage($path)['xpath'];
    }

    private function sourcePage(string $path): array
    {
        $lastException = null;

        foreach ($this->sourceBaseUrls() as $baseUrl) {
            try {
                $response = $this->sourceClient($baseUrl)->get($path)->throw();
                $html = $response->body();

                if (trim($html) === '' || ! $this->isUsableSourceHtml($path, $html)) {
                    continue;
                }

                libxml_use_internal_errors(true);

                $dom = new \DOMDocument();
                $dom->loadHTML($html);
                libxml_clear_errors();

                return [
                    'html' => $html,
                    'xpath' => new \DOMXPath($dom),
                    'url' => $this->extractPageUrl($html, rtrim($baseUrl, '/') . '/' . ltrim($path, '/')),
                ];
            } catch (Throwable $exception) {
                $lastException = $exception;
            }
        }

        throw $lastException ?: new \RuntimeException('Unable to load Otakudesu source page.');
    }

    private function isUsableSourceHtml(string $path, string $html): bool
    {
        $normalizedPath = '/' . ltrim($path, '/');
        $haystack = Str::lower($html);

        if (str_contains($haystack, 'blocked') || str_contains($haystack, 'access denied')) {
            return false;
        }

        if ($normalizedPath === '/' || $normalizedPath === '') {
            return str_contains($html, '/anime/') || str_contains($haystack, 'rseries') || str_contains($haystack, 'venz');
        }

        if ($normalizedPath === '/anime-list/') {
            return str_contains($html, '/anime/');
        }

        if ($normalizedPath === '/genre-list/') {
            return str_contains($html, '/genres/');
        }

        if (
            str_contains($normalizedPath, '/ongoing-anime/')
            || str_contains($normalizedPath, '/complete-anime/')
            || str_contains($normalizedPath, '/genres/')
            || str_starts_with($normalizedPath, '/search/')
        ) {
            return str_contains($html, '/anime/');
        }

        if (str_starts_with($normalizedPath, '/anime/')) {
            return str_contains($html, '/eps/') || str_contains($haystack, 'episode') || str_contains($haystack, 'sinopsis');
        }

        if (str_starts_with($normalizedPath, '/episode/') || str_starts_with($normalizedPath, '/eps/')) {
            return str_contains($haystack, 'mirrorstream') || str_contains($haystack, 'download') || str_contains($haystack, 'stream');
        }

        return true;
    }

    private function parsePosterListItems(\DOMXPath $xpath, string $query, ?\DOMNode $contextNode = null): array
    {
        $items = [];

        foreach ($xpath->query($query, $contextNode) as $node) {
            $href = $this->queryAttr($xpath, ".//div[contains(@class,'thumb')]//a", 'href', $node);
            $id = $this->extractSiteId($href, 'anime');

            if ($id === '') {
                continue;
            }

            $items[] = [
                'id' => $id,
                'title' => $this->queryText($xpath, ".//h2[contains(@class,'jdlflm')]", $node),
                'thumb' => $this->queryAttr($xpath, ".//img", 'src', $node),
                'episode' => $this->queryText($xpath, ".//div[contains(@class,'epz')]", $node),
                'score' => (float) ($this->queryText($xpath, ".//div[contains(@class,'epztipe')]", $node) ?: 0),
                'day_updated' => $this->queryText($xpath, ".//div[contains(@class,'epztipe')]", $node),
                'uploaded_on' => $this->queryText($xpath, ".//div[contains(@class,'newnime')]", $node),
                'release_date' => $this->queryText($xpath, ".//div[contains(@class,'newnime')]", $node),
            ];
        }

        return $items;
    }

    private function parseGenreAnchors(\DOMXPath $xpath, string $query, \DOMNode $contextNode): array
    {
        $genres = [];

        foreach ($xpath->query($query, $contextNode) as $genre) {
            $href = $genre instanceof \DOMElement ? $genre->getAttribute('href') : '';
            $id = $this->extractSiteId($href, 'genres');

            if ($id === '') {
                continue;
            }

            $genres[] = [
                'genre_id' => $id,
                'genre_name' => $this->cleanText($genre->textContent),
            ];
        }

        return $genres;
    }

    private function parseCommaGenres(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $genre) => trim($genre))
            ->filter()
            ->values()
            ->map(fn (string $genre) => [
                'genre_id' => Str::slug($genre),
                'genre_name' => $genre,
            ])
            ->all();
    }

    private function parseDownloadQualities(\DOMXPath $xpath): array
    {
        $qualities = [];

        foreach ($xpath->query("//div[contains(@class,'download')]//li") as $item) {
            $quality = $this->queryText($xpath, ".//strong", $item);

            if ($quality === null) {
                continue;
            }

            $downloadLinks = [];

            foreach ($xpath->query(".//a", $item) as $link) {
                $downloadLinks[] = [
                    'host' => $this->cleanText($link->textContent),
                    'link' => $link instanceof \DOMElement ? $link->getAttribute('href') : '',
                ];
            }

            $qualities[] = [
                'quality' => $quality,
                'size' => $this->queryText($xpath, ".//i", $item),
                'download_links' => $downloadLinks,
            ];
        }

        return $qualities;
    }

    private function parseMirrorGroups(\DOMXPath $xpath): array
    {
        $groups = [];

        foreach ($xpath->query("//div[contains(@class,'mirrorstream')]/ul") as $groupNode) {
            if (! $groupNode instanceof \DOMElement) {
                continue;
            }

            $className = $groupNode->getAttribute('class');
            preg_match('/m(\d{3,4}p)/i', $className, $qualityMatches);
            $quality = $this->normalizeQualityLabel($qualityMatches[1] ?? $groupNode->textContent);

            if ($quality === '') {
                continue;
            }

            $mirrors = [];

            foreach ($xpath->query(".//a[@data-content]", $groupNode) as $linkNode) {
                if (! $linkNode instanceof \DOMElement) {
                    continue;
                }

                $payload = trim($linkNode->getAttribute('data-content'));

                if ($payload === '') {
                    continue;
                }

                $mirrors[] = [
                    'label' => $this->cleanText($linkNode->textContent) ?: 'Mirror',
                    'data_content' => $payload,
                    'is_default' => filter_var($linkNode->getAttribute('data-default'), FILTER_VALIDATE_BOOLEAN),
                ];
            }

            if ($mirrors === []) {
                continue;
            }

            $groups[] = [
                'quality' => $quality,
                'mirrors' => $mirrors,
            ];
        }

        usort($groups, fn (array $left, array $right) => $this->qualityRank($right['quality'] ?? '') <=> $this->qualityRank($left['quality'] ?? ''));

        return array_values($groups);
    }

    private function resolveStreamOptions(array $mirrorGroups, string $html, string $pageUrl): array
    {
        $ajaxUrl = $this->extractAjaxUrl($html);

        if ($ajaxUrl === null || $mirrorGroups === []) {
            return [];
        }

        $nonce = $this->fetchMirrorNonce($ajaxUrl, $pageUrl);

        if ($nonce === null) {
            return [];
        }

        $options = [];

        foreach ($mirrorGroups as $group) {
            $resolved = null;

            foreach (data_get($group, 'mirrors', []) as $mirror) {
                $url = $this->resolveMirrorUrl($ajaxUrl, $pageUrl, $nonce, $mirror['data_content'] ?? null);

                if ($url === null) {
                    continue;
                }

                $resolved = [
                    'quality' => $group['quality'] ?? null,
                    'label' => $mirror['label'] ?? 'Mirror',
                    'url' => $url,
                    'is_default' => (bool) ($mirror['is_default'] ?? false),
                ];

                break;
            }

            if ($resolved !== null) {
                $options[] = $resolved;
            }
        }

        usort($options, function (array $left, array $right) {
            $qualityComparison = $this->qualityRank($right['quality'] ?? '') <=> $this->qualityRank($left['quality'] ?? '');

            if ($qualityComparison !== 0) {
                return $qualityComparison;
            }

            return (int) ($right['is_default'] ?? false) <=> (int) ($left['is_default'] ?? false);
        });

        return array_values($options);
    }

    private function extractAjaxUrl(string $html): ?string
    {
        if (preg_match('#https?://[^"\']+/wp-admin/admin-ajax\.php#i', $html, $matches)) {
            return $matches[0];
        }

        return null;
    }

    private function extractPageUrl(string $html, string $fallbackUrl): string
    {
        if (preg_match('/<meta[^>]+property=["\']og:url["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES);
        }

        if (preg_match('/<link[^>]+rel=["\']canonical["\'][^>]+href=["\']([^"\']+)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES);
        }

        return $fallbackUrl;
    }

    private function fetchMirrorNonce(string $ajaxUrl, string $pageUrl): ?string
    {
        $cacheKey = 'otakudesu:mirror_nonce:' . md5($ajaxUrl);

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($ajaxUrl, $pageUrl) {
            $response = $this->mirrorClient($ajaxUrl, $pageUrl)
                ->asForm()
                ->post($ajaxUrl, [
                    'action' => 'aa1208d27f29ca340c92c66d1926f13f',
                ]);

            if (! $response->successful()) {
                return null;
            }

            return data_get($response->json(), 'data');
        });
    }

    private function resolveMirrorUrl(string $ajaxUrl, string $pageUrl, string $nonce, ?string $encodedPayload): ?string
    {
        if ($encodedPayload === null || $encodedPayload === '') {
            return null;
        }

        $cacheKey = 'otakudesu:mirror_url:' . md5($ajaxUrl . '|' . $encodedPayload);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($ajaxUrl, $pageUrl, $nonce, $encodedPayload) {
            $decodedPayload = json_decode(base64_decode($encodedPayload, true) ?: '', true);

            if (! is_array($decodedPayload)) {
                return null;
            }

            $response = $this->mirrorClient($ajaxUrl, $pageUrl)
                ->asForm()
                ->post($ajaxUrl, array_merge($decodedPayload, [
                    'nonce' => $nonce,
                    'action' => '2a3505c93b0035d3f455df82bf976b84',
                ]));

            if (! $response->successful()) {
                return null;
            }

            $embedHtml = base64_decode((string) data_get($response->json(), 'data'), true);

            if (! is_string($embedHtml) || trim($embedHtml) === '') {
                return null;
            }

            if (preg_match('/src=["\']([^"\']+)["\']/i', $embedHtml, $matches)) {
                return html_entity_decode($matches[1], ENT_QUOTES);
            }

            return null;
        });
    }

    private function mirrorClient(string $ajaxUrl, string $pageUrl): PendingRequest
    {
        return Http::withHeaders([
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Origin' => $this->baseOrigin($ajaxUrl),
                'Referer' => $pageUrl,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->withOptions($this->httpOptions())
            ->connectTimeout(5)
            ->timeout(10);
    }

    private function selectStreamOption(Collection $streamOptions, ?string $preferredQuality = null): ?array
    {
        if ($streamOptions->isEmpty()) {
            return null;
        }

        $preferredQuality = $this->normalizeQualityLabel((string) $preferredQuality);

        if ($preferredQuality !== '') {
            $preferred = $streamOptions->first(fn (array $option) => $this->normalizeQualityLabel((string) ($option['quality'] ?? '')) === $preferredQuality);

            if ($preferred) {
                return $preferred;
            }
        }

        return $streamOptions
            ->sort(function (array $left, array $right) {
                $qualityComparison = $this->qualityRank($right['quality'] ?? '') <=> $this->qualityRank($left['quality'] ?? '');

                if ($qualityComparison !== 0) {
                    return $qualityComparison;
                }

                return (int) ($right['is_default'] ?? false) <=> (int) ($left['is_default'] ?? false);
            })
            ->first();
    }

    private function normalizeQualityLabel(string $quality): string
    {
        if (preg_match('/(\d{3,4})/i', $quality, $matches)) {
            return $matches[1] . 'p';
        }

        return Str::lower(trim($quality));
    }

    private function qualityRank(string $quality): int
    {
        if (preg_match('/(\d{3,4})/i', $quality, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function baseOrigin(string $url): string
    {
        $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        $port = parse_url($url, PHP_URL_PORT);

        if ($host === '') {
            return $scheme . '://';
        }

        return $scheme . '://' . $host . ($port ? ':' . $port : '');
    }

    private function inferStatusFromEpisodeLabel(?string $value): string
    {
        $value = Str::lower((string) $value);

        if (str_contains($value, 'unknown') || str_contains($value, '?')) {
            return 'ongoing';
        }

        return 'completed';
    }

    private function catalogPerPage(string $source): int
    {
        return in_array($source, ['all', 'genre-all', 'genre-fast'], true) ? 48 : 25;
    }

    private function shouldUseFastGenrePath(string $year, string $minScore): bool
    {
        return $year === '' && $minScore === '';
    }

    private function warmAnimeIds(): array
    {
        $ids = collect();

        $homeSnapshot = $this->getSnapshot('home');
        $animeListSnapshot = $this->getSnapshot('anime-list');
        $ongoingSnapshot = $this->getSnapshot('ongoing/page/1');
        $completeSnapshot = $this->getSnapshot('complete/page/1');

        $ids = $ids
            ->concat(collect(data_get($homeSnapshot, 'home.on_going', []))->pluck('id'))
            ->concat(collect(data_get($homeSnapshot, 'home.complete', []))->pluck('id'))
            ->concat(collect(data_get($animeListSnapshot, 'animeList', []))->pluck('id'))
            ->concat(collect(data_get($ongoingSnapshot, 'animeList', []))->pluck('id'))
            ->concat(collect(data_get($completeSnapshot, 'animeList', []))->pluck('id'));

        return $ids
            ->map(fn ($id) => $this->sanitizeId((string) $id))
            ->filter()
            ->unique()
            ->take(10)
            ->values()
            ->all();
    }

    private function storeSnapshot(string $path, array $payload): void
    {
        if ($payload === [] || ! Schema::hasTable('scraper_snapshots')) {
            return;
        }

        ScraperSnapshot::query()->updateOrCreate(
            ['snapshot_key' => $this->snapshotKey($path)],
            [
                'path' => $path,
                'payload' => $payload,
                'stored_at' => now(),
            ]
        );
    }

    private function getSnapshot(string $path): array
    {
        if (! Schema::hasTable('scraper_snapshots')) {
            return [];
        }

        $snapshot = ScraperSnapshot::query()
            ->where('snapshot_key', $this->snapshotKey($path))
            ->first();

        return is_array($snapshot?->payload) ? $snapshot->payload : [];
    }

    private function snapshotKey(string $path): string
    {
        return md5($path);
    }

    private function cacheKeyForPath(string $path): string
    {
        return match (true) {
            $path === 'home' => 'home',
            $path === 'genres' => 'genres',
            $path === 'anime-list' => 'anime-list',
            str_starts_with($path, 'search/') => 'search:' . md5((string) Str::after($path, 'search/')),
            str_starts_with($path, 'complete/page/') => 'complete:' . (int) Str::after($path, 'complete/page/'),
            str_starts_with($path, 'ongoing/page/') => 'ongoing:' . (int) Str::after($path, 'ongoing/page/'),
            str_starts_with($path, 'genres/') && str_ends_with($path, '/catalog') => 'genre-catalog:' . Str::between($path, 'genres/', '/catalog'),
            str_starts_with($path, 'genres/') && str_contains($path, '/catalog/page/') => 'genre-catalog:' . Str::between($path, 'genres/', '/catalog/page/') . ':page:' . (int) Str::after($path, '/catalog/page/'),
            str_starts_with($path, 'anime/') => 'anime:' . md5((string) Str::after($path, 'anime/')),
            str_starts_with($path, 'eps/') => 'episode:' . md5((string) Str::after($path, 'eps/')),
            default => md5($path),
        };
    }

    private function sourceBaseUrls(): array
    {
        $configured = rtrim((string) config('services.otakudesu.source_url', 'https://otakudesu.best'), '/');

        return array_values(array_unique(array_filter([
            $configured,
            'https://otakudesu.blog',
            'https://otakudesu.best',
            'https://otakudesu.cloud',
            'https://otakudesu.moe',
        ])));
    }

    private function extractLabeledValue(\DOMXPath $xpath, \DOMNode $contextNode, string $label): ?string
    {
        foreach ($xpath->query(".//div[contains(@class,'set')]", $contextNode) as $node) {
            $text = $this->cleanText($node->textContent);

            if (Str::startsWith(Str::lower($text), Str::lower($label))) {
                return trim((string) Str::of($text)->after(':'));
            }
        }

        return null;
    }

    private function queryText(\DOMXPath $xpath, string $query, ?\DOMNode $contextNode = null): ?string
    {
        $node = $xpath->query($query, $contextNode)->item(0);

        if (! $node) {
            return null;
        }

        return $this->cleanText($node->textContent);
    }

    private function queryAttr(\DOMXPath $xpath, string $query, string $attribute, ?\DOMNode $contextNode = null): ?string
    {
        $node = $xpath->query($query, $contextNode)->item(0);

        if (! $node instanceof \DOMElement) {
            return null;
        }

        return $node->getAttribute($attribute) ?: null;
    }

    private function cleanText(?string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', html_entity_decode((string) $value)));
    }

    private function posterImage(?string $url): string
    {
        return $this->imageOrFallback($url, 'https://placehold.co/600x900/09090b/f97316?text=No+Image');
    }

    private function bannerImage(?string $url): string
    {
        return $this->imageOrFallback($url, 'https://placehold.co/1400x700/111827/f43f5e?text=No+Banner');
    }

    private function imageOrFallback(?string $url, string $fallback): string
    {
        $url = trim((string) $url);

        if ($url === '' || $url === 'null') {
            return $fallback;
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if (in_array($host, [
                'otakudesu.best',
                'otakudesu.blog',
                'otakudesu.cloud',
                'otakudesu.moe',
                'www.otakudesu.best',
                'www.otakudesu.blog',
                'www.otakudesu.cloud',
                'www.otakudesu.moe',
            ], true)) {
                if (! (bool) config('services.otakudesu.proxy_images', false)) {
                    return $url;
                }

                return route('image.proxy', ['url' => $url]);
            }
        }

        return $url;
    }

    private function isFallbackImage(string $url): bool
    {
        return str_contains($url, 'placehold.co');
    }

    private function extractSiteId(?string $url, string $segment): string
    {
        $path = parse_url((string) $url, PHP_URL_PATH) ?: '';
        $pattern = '#/' . preg_quote(trim($segment, '/'), '#') . '/([^/]+)/?#';

        if (preg_match($pattern, $path, $matches)) {
            return trim($matches[1], '/');
        }

        return '';
    }

    private function probe(callable $callback): array
    {
        try {
            $response = $callback();

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
            ];
        } catch (Throwable $exception) {
            return [
                'ok' => false,
                'status' => null,
                'message' => $exception->getMessage(),
            ];
        }
    }
}
