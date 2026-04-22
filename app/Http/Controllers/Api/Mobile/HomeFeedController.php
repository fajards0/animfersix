<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Services\OtakudesuApiService;
use Illuminate\Http\JsonResponse;

class HomeFeedController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function __invoke(): JsonResponse
    {
        $home = $this->api->homeData();
        $catalog = $this->api->catalog([], 1);

        return response()->json([
            'status' => 'success',
            'app' => [
                'name' => config('app.name', 'Fer6origami'),
                'tagline' => 'Anime catalog atelier',
            ],
            'home' => [
                'featured' => $this->serializeAnimeCollection($home->popular->take(1)),
                'trending' => $this->serializeAnimeCollection($home->trending->take(8)),
                'popular' => $this->serializeAnimeCollection($home->popular->take(8)),
                'ongoing' => $this->serializeAnimeCollection($home->ongoing->take(10)),
                'complete' => $this->serializeAnimeCollection($home->complete->take(10)),
                'catalog_highlights' => $this->serializeAnimeCollection($catalog->items->take(12)),
                'latest_episodes' => $home->latestEpisodes->take(8)->map(function ($episode) {
                    return [
                        'title' => $episode->title,
                        'episode_number' => $episode->episode_number,
                        'uploaded_on' => $episode->uploaded_on,
                        'route_id' => $episode->route_id,
                        'anime_route_id' => $episode->anime_route_id,
                        'anime' => [
                            'title' => $episode->anime->title,
                            'poster_image' => $episode->anime->poster_image,
                        ],
                    ];
                })->values(),
            ],
        ]);
    }

    private function serializeAnimeCollection($collection)
    {
        return $collection->map(function ($anime) {
            return [
                'id' => $anime->id,
                'route_id' => $anime->route_id,
                'title' => $anime->title,
                'poster_image' => $anime->poster_image,
                'banner_image' => $anime->banner_image,
                'synopsis' => $anime->synopsis,
                'studio' => $anime->studio,
                'year' => $anime->year,
                'status' => $anime->status,
                'score' => $anime->score,
                'type' => $anime->type,
                'episode_label' => $anime->episode_label,
                'genres' => collect($anime->genres)->map(fn ($genre) => [
                    'id' => $genre->id ?? null,
                    'name' => $genre->name ?? null,
                ])->values(),
            ];
        })->values();
    }
}
