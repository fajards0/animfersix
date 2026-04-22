<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GeneratesUniqueSlug;
use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Episode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EpisodeController extends Controller
{
    use GeneratesUniqueSlug;

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $episodes = Episode::query()
            ->with('anime')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhereHas('anime', fn ($animeQuery) => $animeQuery->where('title', 'like', '%' . $search . '%'));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.episodes.index', [
            'episodes' => $episodes,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.episodes.create', [
            'episode' => new Episode(),
            'animes' => Anime::query()->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->generateUniqueSlug($data['title'] . '-' . $data['episode_number'], Episode::class);
        $data['is_published'] = $request->boolean('is_published');

        Episode::create($data);

        return redirect()->route('admin.episodes.index')->with('success', 'Episode berhasil ditambahkan.');
    }

    public function edit(Episode $episode): View
    {
        return view('admin.episodes.edit', [
            'episode' => $episode,
            'animes' => Anime::query()->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Episode $episode): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->generateUniqueSlug($data['title'] . '-' . $data['episode_number'], Episode::class, $episode);
        $data['is_published'] = $request->boolean('is_published');

        $episode->update($data);

        return redirect()->route('admin.episodes.index')->with('success', 'Episode berhasil diperbarui.');
    }

    public function destroy(Episode $episode): RedirectResponse
    {
        $episode->delete();

        return redirect()->route('admin.episodes.index')->with('success', 'Episode berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'anime_id' => ['required', 'integer', 'exists:animes,id'],
            'title' => ['required', 'string', 'max:255'],
            'episode_number' => ['required', 'integer', 'min:1'],
            'synopsis' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'aired_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }
}
