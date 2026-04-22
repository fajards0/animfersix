<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScraperHealthApiTest extends TestCase
{
    public function test_scraper_health_reports_api_and_source_status(): void
    {
        Cache::flush();

        Http::fake([
            'http://127.0.0.1:3000/api/home' => Http::response(['status' => 'success'], 200),
            'https://otakudesu.best/' => Http::response('<html></html>', 200),
        ]);

        $response = $this->getJson('/api/scraper-health');

        $response->assertOk()->assertJson([
            'configured' => [
                'api_base_url' => 'http://127.0.0.1:3000/api',
                'source_url' => 'https://otakudesu.best',
                'proxy_disabled' => true,
            ],
            'checks' => [
                'api' => [
                    'ok' => true,
                    'status' => 200,
                ],
                'source' => [
                    'ok' => true,
                    'status' => 200,
                ],
            ],
            'cache' => [
                'unavailable' => false,
                'last_error' => null,
            ],
        ]);
    }

    public function test_scraper_health_exposes_cached_last_error(): void
    {
        Cache::flush();
        Cache::put('otakudesu:unavailable', true, now()->addSeconds(30));
        Cache::put('otakudesu:last_error', [
            'path' => 'home',
            'message' => 'Connection refused',
            'time' => now()->toIso8601String(),
        ], now()->addMinutes(10));

        Http::fake([
            'http://127.0.0.1:3000/api/home' => Http::response([], 500),
            'https://otakudesu.best/' => Http::response('Too many requests', 429),
        ]);

        $response = $this->getJson('/api/scraper-health');

        $response->assertOk()->assertJson([
            'checks' => [
                'api' => [
                    'ok' => false,
                    'status' => 500,
                ],
                'source' => [
                    'ok' => false,
                    'status' => 429,
                ],
            ],
            'cache' => [
                'unavailable' => true,
                'last_error' => [
                    'path' => 'home',
                    'message' => 'Connection refused',
                ],
            ],
        ]);
    }
}
