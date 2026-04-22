<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\OtakudesuApiService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

class HomeController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function index(): View
    {
        try {
            $home = $this->api->homeData();
        } catch (Throwable) {
            $home = new \Illuminate\Support\Fluent([
                'trending' => collect(),
                'latestEpisodes' => collect(),
                'popular' => collect(),
                'ongoing' => collect(),
                'complete' => collect(),
            ]);
        }

        try {
            $catalog = $this->api->catalog([], 1);
        } catch (Throwable) {
            $catalog = new \Illuminate\Support\Fluent([
                'items' => collect(),
                'pagination' => new \Illuminate\Support\Fluent([
                    'paginator' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 48, 1),
                ]),
            ]);
        }

        try {
            $genres = $this->api->getGenres();
        } catch (Throwable) {
            $genres = collect();
        }

        return view('home', [
            'banners' => $this->banners(),
            'trendingAnimes' => $home->trending->take(8),
            'latestEpisodes' => $home->latestEpisodes,
            'popularAnimes' => $home->popular->take(6),
            'ongoingAnimes' => $home->ongoing->take(10),
            'completeAnimes' => $home->complete->take(10),
            'catalogHighlights' => $catalog->items->take(12),
            'catalogTotal' => $catalog->pagination->paginator->total(),
            'filters' => [
                'genres' => $genres,
                'statuses' => [
                    'ongoing' => 'Ongoing',
                    'completed' => 'Completed',
                ],
                'ratings' => $this->api->scoreOptions(),
                'years' => $this->api->yearOptions(),
            ],
        ]);
    }

    private function banners(): Collection
    {
        try {
            return Banner::query()->active()->orderBy('sort_order')->get();
        } catch (Throwable) {
            return collect();
        }
    }
}
