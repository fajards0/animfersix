<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CompleteAnimeApiTest extends TestCase
{
    public function test_complete_anime_api_returns_upstream_payload_shape(): void
    {
        Cache::flush();

        Http::fake([
            'http://127.0.0.1:3000/api/complete/page/1' => Http::response([
                'status' => 'success',
                'baseUrl' => 'https://otakudesu.moe/complete-anime/',
                'animeList' => [
                    [
                        'id' => 'naruto',
                        'title' => 'Naruto',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/complete-anime');

        $response->assertOk()->assertExactJson([
            'status' => 'success',
            'baseUrl' => 'https://otakudesu.moe/complete-anime/',
            'animeList' => [
                [
                    'id' => 'naruto',
                    'title' => 'Naruto',
                ],
            ],
        ]);
    }

    public function test_complete_anime_api_returns_default_shape_when_source_fails(): void
    {
        Cache::flush();

        Http::fake([
            'http://127.0.0.1:3000/api/complete/page/1' => Http::response([], 500),
            'https://otakudesu.best/complete-anime/' => Http::response('blocked', 429),
            'https://otakudesu.blog/complete-anime/' => Http::response('blocked', 429),
            'https://otakudesu.cloud/complete-anime/' => Http::response('blocked', 429),
            'https://otakudesu.moe/complete-anime/' => Http::response('blocked', 429),
        ]);

        $response = $this->getJson('/api/complete-anime');

        $response->assertOk()->assertExactJson([
            'status' => 'success',
            'baseUrl' => 'https://otakudesu.moe/complete-anime/',
            'animeList' => [],
        ]);
    }
}
