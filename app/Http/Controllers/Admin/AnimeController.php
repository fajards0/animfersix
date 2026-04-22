<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GeneratesUniqueSlug;
use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnimeController extends Controller
{
    use GeneratesUniqueSlug;

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $animes = Anime::query()
            ->with('genres')
            ->when($search, fn ($query) => $query->where('title', 'like', '%' . $search . '%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.animes.index', [
            'animes' => $animes,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.animes.create', [
            'anime' => new Anime(),
            'genres' => Genre::query()->orderBy('name')->get(),
            'statuses' => Anime::STATUSES,
            'ratings' => Anime::RATINGS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->generateUniqueSlug($data['title'], Anime::class);
        $data['poster_path'] = $this->storeImage($request, 'poster_upload', 'animes/posters', $data['poster_path']);
        $data['banner_path'] = $this->storeImage($request, 'banner_upload', 'animes/banners', $data['banner_path']);
        $data['is_trending'] = $request->boolean('is_trending');
        $data['is_popular'] = $request->boolean('is_popular');

        $anime = Anime::create($data);
        $anime->genres()->sync($request->input('genre_ids', []));

        return redirect()->route('admin.animes.index')->with('success', 'Anime berhasil ditambahkan.');
    }

    public function edit(Anime $anime): View
    {
        $anime->load('genres');

        return view('admin.animes.edit', [
            'anime' => $anime,
            'genres' => Genre::query()->orderBy('name')->get(),
            'statuses' => Anime::STATUSES,
            'ratings' => Anime::RATINGS,
        ]);
    }

    public function update(Request $request, Anime $anime): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->generateUniqueSlug($data['title'], Anime::class, $anime);
        $data['poster_path'] = $this->storeImage($request, 'poster_upload', 'animes/posters', $data['poster_path'], $anime->poster_path);
        $data['banner_path'] = $this->storeImage($request, 'banner_upload', 'animes/banners', $data['banner_path'], $anime->banner_path);
        $data['is_trending'] = $request->boolean('is_trending');
        $data['is_popular'] = $request->boolean('is_popular');

        $anime->update($data);
        $anime->genres()->sync($request->input('genre_ids', []));

        return redirect()->route('admin.animes.index')->with('success', 'Anime berhasil diperbarui.');
    }

    public function destroy(Anime $anime): RedirectResponse
    {
        $this->deleteStoredFile($anime->poster_path);
        $this->deleteStoredFile($anime->banner_path);
        $anime->delete();

        return redirect()->route('admin.animes.index')->with('success', 'Anime berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'synopsis' => ['required', 'string'],
            'studio' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 2)],
            'status' => ['required', 'in:' . implode(',', array_keys(Anime::STATUSES))],
            'rating' => ['required', 'in:' . implode(',', array_keys(Anime::RATINGS))],
            'score' => ['required', 'numeric', 'min:0', 'max:10'],
            'type' => ['required', 'string', 'max:50'],
            'views' => ['nullable', 'integer', 'min:0'],
            'poster_path' => ['nullable', 'string', 'max:2048'],
            'banner_path' => ['nullable', 'string', 'max:2048'],
            'poster_upload' => ['nullable', 'image', 'max:2048'],
            'banner_upload' => ['nullable', 'image', 'max:3072'],
            'genre_ids' => ['nullable', 'array'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
            'is_trending' => ['nullable', 'boolean'],
            'is_popular' => ['nullable', 'boolean'],
        ]);
    }

    private function storeImage(Request $request, string $field, string $directory, ?string $fallback = null, ?string $oldValue = null): ?string
    {
        if (! $request->hasFile($field)) {
            return $fallback;
        }

        $this->deleteStoredFile($oldValue);

        return $request->file($field)->store($directory, 'public');
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
