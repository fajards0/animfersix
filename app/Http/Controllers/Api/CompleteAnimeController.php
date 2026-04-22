<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OtakudesuApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompleteAnimeController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            $this->api->completeAnimeApi($request->integer('page', 1))
        );
    }
}
