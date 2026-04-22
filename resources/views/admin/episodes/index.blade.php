@extends('layouts.app')

@section('title', 'Fer6origami | Admin Episode')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin Episode</p>
                <h1 class="mt-2 font-display text-4xl font-bold text-white">Kelola episode</h1>
            </div>
            <a href="{{ route('admin.episodes.create') }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                Tambah Episode
            </a>
        </div>

        <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
            <form action="{{ route('admin.episodes.index') }}" method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari episode atau anime..." class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-500">
                <button type="submit" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">Cari</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase tracking-[0.3em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Anime</th>
                            <th class="px-4 py-3">Episode</th>
                            <th class="px-4 py-3">Publish</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($episodes as $episode)
                            <tr class="border-t border-white/10">
                                <td class="px-4 py-4">{{ $episode->anime->title }}</td>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-white">Ep {{ $episode->episode_number }}</p>
                                    <p class="text-xs text-slate-500">{{ $episode->title }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $episode->is_published ? 'Published' : 'Draft' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.episodes.edit', $episode) }}" class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/5">Edit</a>
                                        <form action="{{ route('admin.episodes.destroy', $episode) }}" method="POST">
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

            <div class="mt-6">{{ $episodes->links() }}</div>
        </div>
    </section>
@endsection
