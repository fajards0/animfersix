@csrf

<div class="space-y-5">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-300">Judul</label>
        <input type="text" name="title" value="{{ old('title', $banner->title) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-300">Subtitle</label>
        <textarea name="subtitle" rows="4" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">{{ old('subtitle', $banner->subtitle) }}</textarea>
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-300">Image URL atau path</label>
        <input type="text" name="image_path" value="{{ old('image_path', $banner->image_path) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-300">Upload Banner</label>
        <input type="file" name="image_upload" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Teks Tombol</label>
            <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Link Tombol</label>
            <input type="text" name="button_link" value="{{ old('button_link', $banner->button_link) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-300">Urutan</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
        </div>
    </div>
    <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true)) class="rounded border-white/10 bg-white/5 text-ember-500">
        Banner aktif
    </label>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">Simpan</button>
    <a href="{{ route('admin.banners.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">Batal</a>
</div>
