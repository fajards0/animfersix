<?php

namespace App\Http\Controllers;

use App\Services\OtakudesuApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\View\View;
use Throwable;

class AnimeCatalogController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'genre', 'year', 'status', 'rating']);
        $page = max(1, (int) $request->integer('page', 1));

        try {
            $catalog = $this->api->catalog($filters, $page);
        } catch (Throwable) {
            $catalog = new Fluent([
                'items' => collect(),
                'pagination' => new Fluent([
                    'paginator' => new LengthAwarePaginator([], 0, 48, $page, [
                        'path' => route('anime.index'),
                        'query' => $request->query(),
                    ]),
                    'current_page' => $page,
                    'previous_page' => $page > 1 ? $page - 1 : null,
                    'next_page' => null,
                ]),
            ]);
        }

        try {
            $genres = $this->api->getGenres();
        } catch (Throwable) {
            $genres = collect();
        }

        return view('anime.index', [
            'animes' => $catalog->items,
            'pagination' => $catalog->pagination,
            'catalogTotal' => $catalog->pagination->paginator->total(),
            'selectedFilters' => $filters,
            'genres' => $genres,
            'statuses' => [
                'ongoing' => 'Ongoing',
                'completed' => 'Completed',
            ],
            'ratings' => $this->api->scoreOptions(),
            'years' => $this->api->yearOptions(),
        ]);
    }

    public function show(string $animeId): View
    {
        try {
            $anime = $this->api->animeDetail($this->api->decodeId($animeId));
        } catch (Throwable) {
            $anime = $this->fallbackAnime($animeId);
        }

        try {
            $relatedAnimes = $this->api->relatedAnime($anime);
        } catch (Throwable) {
            $relatedAnimes = collect();
        }

        $isInWatchlist = auth()->check()
            ? auth()->user()->watchlists()->where('anime_api_id', $anime->id)->exists()
            : false;

        return view('anime.show', [
            'anime' => $anime,
            'relatedAnimes' => $relatedAnimes,
            'isInWatchlist' => $isInWatchlist,
        ]);
    }

    private function fallbackAnime(string $animeId): Fluent
    {
        $decodedId = $this->api->decodeId($animeId);

        return new Fluent([
            'id' => $decodedId,
            'route_id' => $this->api->encodeId($decodedId),
            'title' => 'Anime sementara tidak tersedia',
            'poster_image' => 'https://placehold.co/600x900/09090b/f97316?text=Anime',
            'banner_image' => 'https://placehold.co/1400x700/111827/f43f5e?text=Fer6origami',
            'synopsis' => 'Data anime ini belum berhasil dimuat dari source saat ini. Coba refresh lagi sebentar.',
            'japanese' => null,
            'score' => 0,
            'producer' => null,
            'type' => null,
            'status' => 'unknown',
            'total_episode' => null,
            'duration' => null,
            'release_date' => null,
            'year' => null,
            'studio' => null,
            'views' => 0,
            'genres' => collect(),
            'episodes' => collect(),
        ]);
    }
}
