<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Services\OtakudesuApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function __construct(private readonly OtakudesuApiService $api)
    {
    }

    public function index(): View
    {
        return view('watchlist.index', [
            'watchlists' => auth()->user()->watchlists()->latest()->paginate(12),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'anime_id' => ['required', 'string'],
        ]);

        $anime = $this->api->animeDetail($data['anime_id']);

        auth()->user()->watchlists()->updateOrCreate(
            ['anime_api_id' => $anime->id],
            [
                'anime_title' => $anime->title,
                'poster_path' => $anime->poster_image,
                'anime_url' => $anime->route_id,
                'score' => $anime->score,
                'status' => $anime->status,
                'type' => $anime->type,
                'studio' => $anime->studio,
                'year' => $anime->year,
                'genres' => $anime->genres->pluck('name')->all(),
            ]
        );

        return back()->with('success', $anime->title . ' ditambahkan ke watchlist.');
    }

    public function destroy(Watchlist $watchlist): RedirectResponse
    {
        abort_unless($watchlist->user_id === auth()->id(), 403);

        $title = $watchlist->anime_title;
        $watchlist->delete();

        return back()->with('success', $title . ' dihapus dari watchlist.');
    }
}
