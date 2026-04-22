<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImageProxyController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $url = trim((string) $request->query('url', ''));

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            abort(404);
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (! in_array($scheme, ['http', 'https'], true)) {
            abort(404);
        }

        $allowedHosts = [
            'otakudesu.best',
            'otakudesu.blog',
            'otakudesu.cloud',
            'otakudesu.moe',
            'www.otakudesu.best',
            'www.otakudesu.blog',
            'www.otakudesu.cloud',
            'www.otakudesu.moe',
        ];

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if (! in_array($host, $allowedHosts, true)) {
            abort(404);
        }

        $payload = Cache::remember('image-proxy:' . md5($url), now()->addHours(12), function () use ($url) {
            $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                    'Referer' => rtrim(config('services.otakudesu.source_url', 'https://otakudesu.best'), '/') . '/',
                ])
                ->withOptions([
                    'proxy' => '',
                    'verify' => false,
                ])
                ->connectTimeout(5)
                ->timeout(15)
                ->get($url);

            if (! $response->successful()) {
                throw new RequestException($response);
            }

            return [
                'body' => base64_encode($response->body()),
                'content_type' => $response->header('Content-Type', 'image/jpeg'),
            ];
        });

        return response(base64_decode((string) ($payload['body'] ?? ''), true) ?: '', 200, [
            'Content-Type' => (string) ($payload['content_type'] ?? 'image/jpeg'),
            'Cache-Control' => 'public, max-age=43200',
        ]);
    }
}
