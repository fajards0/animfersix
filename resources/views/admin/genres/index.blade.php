@extends('layouts.app')

@section('title', 'Fer6origami | Admin Genre')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin Genre</p>
                <h1 class="mt-2 font-display text-4xl font-bold text-white">Kelola genre</h1>
            </div>
            <a href="{{ route('admin.genres.create') }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                Tambah Genre
            </a>
        </div>

        <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase tracking-[0.3em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Genre</th>
                            <th class="px-4 py-3">Anime Count</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($genres as $genre)
                            <tr class="border-t border-white/10">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-white">{{ $genre->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $genre->description }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $genre->animes_count }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.genres.edit', $genre) }}" class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/5">Edit</a>
                                        <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST">
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

            <div class="mt-6">{{ $genres->links() }}</div>
        </div>
    </section>
@endsection
