@extends('layouts.app')

@section('title', 'Fer6origami | Tambah Genre')

@section('content')
    <section class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
        <h1 class="font-display text-4xl font-bold text-white">Tambah genre</h1>
        <form action="{{ route('admin.genres.store') }}" method="POST" class="mt-8 space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Nama Genre</label>
                <input type="text" name="name" value="{{ old('name', $genre->name) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Deskripsi</label>
                <textarea name="description" rows="5" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">{{ old('description', $genre->description) }}</textarea>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">Simpan</button>
                <a href="{{ route('admin.genres.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">Batal</a>
            </div>
        </form>
    </section>
@endsection
