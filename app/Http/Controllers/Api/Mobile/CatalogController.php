<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Services\OtakudesuApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'genre', 'year', 'status', 'rating']);
        $catalog = $this->api->catalog($filters, max(1, $request->integer('page', 1)));

        return response()->json([
            'status' => 'success',
            'filters' => $filters,
            'pagination' => [
                'current_page' => $catalog->pagination->current_page,
                'previous_page' => $catalog->pagination->previous_page,
                'next_page' => $catalog->pagination->next_page,
                'per_page' => $catalog->pagination->paginator->perPage(),
                'total' => $catalog->pagination->paginator->total(),
                'last_page' => $catalog->pagination->paginator->lastPage(),
            ],
            'items' => collect($catalog->items)->map(function ($anime) {
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
                ];
            })->values(),
        ]);
    }
}
