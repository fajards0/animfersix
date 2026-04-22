<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Banner;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'animes' => Anime::count(),
                'episodes' => Episode::count(),
                'genres' => Genre::count(),
                'users' => User::count(),
                'banners' => Banner::count(),
            ],
            'latestAnimes' => Anime::query()->latest()->take(5)->get(),
            'latestEpisodes' => Episode::query()->with('anime')->latest()->take(5)->get(),
            'latestUsers' => User::query()->latest()->take(5)->get(),
        ]);
    }
}
