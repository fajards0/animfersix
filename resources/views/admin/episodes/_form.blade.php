@csrf

<div class="grid gap-6 xl:grid-cols-2">
    <div class="space-y-5">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Anime</label>
            <select name="anime_id" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                @foreach ($animes as $animeOption)
                    <option value="{{ $animeOption->id }}" @selected((string) old('anime_id', $episode->anime_id) === (string) $animeOption->id)>{{ $animeOption->title }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Judul Episode</label>
            <input type="text" name="title" value="{{ old('title', $episode->title) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Nomor Episode</label>
                <input type="number" name="episode_number" value="{{ old('episode_number', $episode->episode_number) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Durasi (menit)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $episode->duration_minutes) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Video URL</label>
            <input type="url" name="video_url" value="{{ old('video_url', $episode->video_url) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Tanggal Tayang</label>
            <input type="datetime-local" name="aired_at" value="{{ old('aired_at', optional($episode->aired_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Synopsis</label>
            <textarea name="synopsis" rows="5" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">{{ old('synopsis', $episode->synopsis) }}</textarea>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $episode->is_published ?? true)) class="rounded border-white/10 bg-white/5 text-ember-500">
            Publish episode
        </label>
    </div>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
        Simpan
    </button>
    <a href="{{ route('admin.episodes.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">
        Batal
    </a>
</div>
