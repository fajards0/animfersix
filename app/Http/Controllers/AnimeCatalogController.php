<?php

namespace App\Http\Controllers;

use App\Services\OtakudesuApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnimeCatalogController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'genre', 'year', 'status', 'rating']);
        $catalog = $this->api->catalog($filters, max(1, (int) $request->integer('page', 1)));

        return view('anime.index', [
            'animes' => $catalog->items,
            'pagination' => $catalog->pagination,
            'catalogTotal' => $catalog->pagination->paginator->total(),
            'selectedFilters' => $filters,
            'genres' => $this->api->getGenres(),
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
        $anime = $this->api->animeDetail($this->api->decodeId($animeId));
        $relatedAnimes = $this->api->relatedAnime($anime);
        $isInWatchlist = auth()->check()
            ? auth()->user()->watchlists()->where('anime_api_id', $anime->id)->exists()
            : false;

        return view('anime.show', [
            'anime' => $anime,
            'relatedAnimes' => $relatedAnimes,
            'isInWatchlist' => $isInWatchlist,
        ]);
    }
}
