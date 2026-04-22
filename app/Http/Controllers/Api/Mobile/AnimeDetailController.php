<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Services\OtakudesuApiService;
use Illuminate\Http\JsonResponse;

class AnimeDetailController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function __invoke(string $animeId): JsonResponse
    {
        $anime = $this->api->animeDetail($this->api->decodeId($animeId));
        $related = $this->api->relatedAnime($anime);

        return response()->json([
            'status' => 'success',
            'anime' => [
                'id' => $anime->id,
                'route_id' => $anime->route_id,
                'title' => $anime->title,
                'poster_image' => $anime->poster_image,
                'banner_image' => $anime->banner_image,
                'synopsis' => $anime->synopsis,
                'japanese' => $anime->japanese,
                'score' => $anime->score,
                'producer' => $anime->producer,
                'type' => $anime->type,
                'status' => $anime->status,
                'total_episode' => $anime->total_episode,
                'duration' => $anime->duration,
                'release_date' => $anime->release_date,
                'year' => $anime->year,
                'studio' => $anime->studio,
                'genres' => $anime->genres->map(fn ($genre) => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                ])->values(),
                'episodes' => $anime->episodes->map(fn ($episode) => [
                    'id' => $episode->id,
                    'route_id' => $episode->route_id,
                    'title' => $episode->title,
                    'episode_number' => $episode->episode_number,
                    'uploaded_on' => $episode->uploaded_on,
                ])->values(),
            ],
            'related' => collect($related)->map(fn ($item) => [
                'id' => $item->id,
                'route_id' => $item->route_id,
                'title' => $item->title,
                'poster_image' => $item->poster_image,
                'studio' => $item->studio,
                'score' => $item->score,
            ])->values(),
        ]);
    }
}
