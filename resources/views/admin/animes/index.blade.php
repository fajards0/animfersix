@extends('layouts.app')

@section('title', 'Fer6origami | Admin Anime')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin Anime</p>
                <h1 class="mt-2 font-display text-4xl font-bold text-white">Kelola anime</h1>
            </div>
            <a href="{{ route('admin.animes.create') }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                Tambah Anime
            </a>
        </div>

        <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
            <form action="{{ route('admin.animes.index') }}" method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul anime..." class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-500 focus:border-ember-500 focus:ring-ember-500/30">
                <button type="submit" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">Cari</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase tracking-[0.3em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Anime</th>
                            <th class="px-4 py-3">Studio</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Score</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($animes as $anime)
                            <tr class="border-t border-white/10">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $anime->poster_image }}" alt="{{ $anime->title }}" class="h-16 w-12 rounded-xl object-cover">
                                        <div>
                                            <p class="font-semibold text-white">{{ $anime->title }}</p>
                                            <p class="text-xs text-slate-500">{{ $anime->year }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">{{ $anime->studio }}</td>
                                <td class="px-4 py-4">{{ $anime->status }}</td>
                                <td class="px-4 py-4">{{ number_format((float) $anime->score, 1) }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.animes.edit', $anime) }}" class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/5">Edit</a>
                                        <form action="{{ route('admin.animes.destroy', $anime) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-red-500/20 px-3 py-2 text-xs font-semibold text-red-200 transition hover:bg-red-500/10">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $animes->links() }}</div>
        </div>
    </section>
@endsection
