<?php

namespace App\Http\Controllers;

use App\Services\OtakudesuApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Illuminate\View\View;
use Throwable;

class WatchController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function show(Request $request, string $animeId, string $episodeId): View
    {
        try {
            $anime = $this->api->animeDetail($this->api->decodeId($animeId));
            $episode = $this->api->episodeDetail(
                $this->api->decodeId($episodeId),
                $anime->id,
                $request->query('quality'),
                $anime
            );
        } catch (Throwable) {
            [$anime, $episode] = $this->fallbackWatchPayload($animeId, $episodeId);
        }

        $currentIndex = collect($anime->episodes)->search(fn ($item) => $item->id === $episode->id);

        $episodes = collect($anime->episodes);

        $previousEpisode = $currentIndex !== false && $currentIndex < $episodes->count() - 1
            ? $episodes->get($currentIndex + 1)
            : null;
        $nextEpisode = $currentIndex !== false && $currentIndex > 0
            ? $episodes->get($currentIndex - 1)
            : null;

        try {
            $recommendations = $this->api->relatedAnime($anime);
        } catch (Throwable) {
            $recommendations = collect();
        }

        return view('watch.show', [
            'episode' => $episode,
            'nextEpisode' => $nextEpisode,
            'previousEpisode' => $previousEpisode,
            'recommendations' => $recommendations,
        ]);
    }

    private function fallbackWatchPayload(string $animeId, string $episodeId): array
    {
        $decodedAnimeId = $this->api->decodeId($animeId);
        $decodedEpisodeId = $this->api->decodeId($episodeId);

        $anime = new Fluent([
            'id' => $decodedAnimeId,
            'route_id' => $this->api->encodeId($decodedAnimeId),
            'title' => 'Episode belum tersedia',
            'poster_image' => 'https://placehold.co/600x900/09090b/f97316?text=Anime',
            'banner_image' => 'https://placehold.co/1400x700/111827/f43f5e?text=Fer6origami',
            'synopsis' => 'Data anime belum berhasil dimuat dari source saat ini.',
            'genres' => collect(),
            'episodes' => collect(),
        ]);

        $episode = new Fluent([
            'id' => $decodedEpisodeId,
            'route_id' => $this->api->encodeId($decodedEpisodeId),
            'title' => 'Episode sementara belum tersedia',
            'stream_url' => null,
            'stream_quality' => null,
            'stream_label' => null,
            'stream_options' => collect(),
            'qualities' => collect(),
            'mirror_groups' => collect(),
            'anime' => $anime,
            'episode_number' => 1,
            'synopsis' => 'Coba refresh lagi beberapa saat. Source streaming sedang belum mengembalikan data lengkap.',
        ]);

        return [$anime, $episode];
    }
}
