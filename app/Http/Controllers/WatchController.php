<?php

namespace App\Http\Controllers;

use App\Services\OtakudesuApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function show(Request $request, string $animeId, string $episodeId): View
    {
        $anime = $this->api->animeDetail($this->api->decodeId($animeId));
        $episode = $this->api->episodeDetail(
            $this->api->decodeId($episodeId),
            $anime->id,
            $request->query('quality'),
            $anime
        );
        $currentIndex = $anime->episodes->search(fn ($item) => $item->id === $episode->id);

        $previousEpisode = $currentIndex !== false && $currentIndex < $anime->episodes->count() - 1
            ? $anime->episodes->get($currentIndex + 1)
            : null;
        $nextEpisode = $currentIndex !== false && $currentIndex > 0
            ? $anime->episodes->get($currentIndex - 1)
            : null;

        return view('watch.show', [
            'episode' => $episode,
            'nextEpisode' => $nextEpisode,
            'previousEpisode' => $previousEpisode,
            'recommendations' => $this->api->relatedAnime($anime),
        ]);
    }
}
