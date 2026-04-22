<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    public function test_home_api_returns_upstream_payload_shape(): void
    {
        Cache::flush();

        Http::fake([
            'http://127.0.0.1:3000/api/home' => Http::response([
                'status' => 'success',
                'baseUrl' => 'https://otakudesu.moe/',
                'home' => [
                    'on_going' => [
                        ['id' => 'one-piece', 'title' => 'One Piece'],
                    ],
                    'complete' => [
                        ['id' => 'naruto', 'title' => 'Naruto'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/home');

        $response->assertOk()->assertExactJson([
            'status' => 'success',
            'baseUrl' => 'https://otakudesu.moe/',
            'home' => [
                'on_going' => [
                    ['id' => 'one-piece', 'title' => 'One Piece'],
                ],
                'complete' => [
                    ['id' => 'naruto', 'title' => 'Naruto'],
                ],
            ],
        ]);
    }

    public function test_home_api_returns_default_shape_when_source_fails(): void
    {
        Cache::flush();

        Http::fake([
            'http://127.0.0.1:3000/api/home' => Http::response([], 500),
            'https://otakudesu.best/' => Http::response('blocked', 429),
            'https://otakudesu.blog/' => Http::response('blocked', 429),
            'https://otakudesu.cloud/' => Http::response('blocked', 429),
            'https://otakudesu.moe/' => Http::response('blocked', 429),
        ]);

        $response = $this->getJson('/api/home');

        $response->assertOk()->assertExactJson([
            'status' => 'success',
            'baseUrl' => 'https://otakudesu.moe/',
            'home' => [
                'on_going' => [],
                'complete' => [],
            ],
        ]);
    }
}
