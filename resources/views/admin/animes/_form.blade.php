@csrf

<div class="grid gap-6 xl:grid-cols-2">
    <div class="space-y-5">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Judul</label>
            <input type="text" name="title" value="{{ old('title', $anime->title) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
            @error('title') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Synopsis</label>
            <textarea name="synopsis" rows="6" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">{{ old('synopsis', $anime->synopsis) }}</textarea>
            @error('synopsis') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Studio</label>
                <input type="text" name="studio" value="{{ old('studio', $anime->studio) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Tahun</label>
                <input type="number" name="year" value="{{ old('year', $anime->year) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Status</label>
                <select name="status" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $anime->status ?: 'ongoing') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Rating</label>
                <select name="rating" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                    @foreach ($ratings as $value => $label)
                        <option value="{{ $value }}" @selected(old('rating', $anime->rating ?: 'PG-13') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Score</label>
                <input type="number" step="0.1" name="score" value="{{ old('score', $anime->score ?: 8.0) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Type</label>
                <input type="text" name="type" value="{{ old('type', $anime->type ?: 'TV') }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Views</label>
            <input type="number" name="views" value="{{ old('views', $anime->views ?: 0) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Genre</label>
            <select name="genre_ids[]" multiple class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                @php($selectedGenres = old('genre_ids', $anime->genres->pluck('id')->all()))
                @foreach ($genres as $genre)
                    <option value="{{ $genre->id }}" @selected(in_array($genre->id, $selectedGenres))>{{ $genre->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Poster URL atau path</label>
            <input type="text" name="poster_path" value="{{ old('poster_path', $anime->poster_path) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Upload Poster</label>
            <input type="file" name="poster_upload" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Banner URL atau path</label>
            <input type="text" name="banner_path" value="{{ old('banner_path', $anime->banner_path) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Upload Banner</label>
            <input type="file" name="banner_upload" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                <input type="checkbox" name="is_trending" value="1" @checked(old('is_trending', $anime->is_trending)) class="rounded border-white/10 bg-white/5 text-ember-500">
                Trending
            </label>
            <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                <input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $anime->is_popular)) class="rounded border-white/10 bg-white/5 text-ember-500">
                Popular
            </label>
        </div>
    </div>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
        Simpan
    </button>
    <a href="{{ route('admin.animes.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">
        Batal
    </a>
</div>
