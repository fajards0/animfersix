@extends('layouts.app')

@section('title', 'Fer6origami | ' . $anime->title)

@section('content')
    <section class="brand-frame overflow-hidden rounded-[38px] border border-white/10 shadow-glow">
        <div class="relative min-h-[620px] overflow-hidden">
            <img src="{{ $anime->banner_image }}" alt="{{ $anime->title }}" data-fallback-src="https://placehold.co/1400x700/111827/f43f5e?text=No+Banner" class="absolute inset-0 h-full w-full object-cover opacity-35">
            <div class="hero-scrim absolute inset-0"></div>

            <div class="relative grid gap-8 p-6 md:p-10 xl:grid-cols-[320px_1fr] xl:gap-10">
                <div class="mx-auto w-full max-w-[320px]">
                    <div class="overflow-hidden rounded-[34px] border border-white/10 bg-white/5 shadow-glow">
                        <img src="{{ $anime->poster_image }}" alt="{{ $anime->title }}" data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image" class="aspect-[4/5] h-full w-full object-cover">
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Score</p>
                            <p class="mt-2 font-display text-3xl font-bold text-white">{{ number_format((float) $anime->score, 1) }}</p>
                        </div>
                        <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Episodes</p>
                            <p class="mt-2 font-display text-3xl font-bold text-white">{{ $anime->episodes->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col justify-end">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex rounded-full border border-ember-500/30 bg-ember-500/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-ember-200">
                            {{ $anime->status ?: 'anime' }}
                        </span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                            {{ $anime->type ?: 'Series' }}
                        </span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                            {{ $anime->year ?: 'TBA' }}
                        </span>
                    </div>

                    <h1 class="mt-6 max-w-4xl font-display text-4xl font-bold leading-tight text-white md:text-6xl">{{ $anime->title }}</h1>
                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-300 md:text-base">{{ $anime->synopsis }}</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($anime->genres as $genre)
                            <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-slate-200">{{ $genre->name }}</span>
                        @endforeach
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @if ($anime->episodes->first())
                            <a href="{{ route('watch.show', ['animeId' => $anime->route_id, 'episodeId' => $anime->episodes->first()->route_id]) }}" class="rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                                Tonton Sekarang
                            </a>
                        @endif

                        @auth
                            @if ($isInWatchlist)
                                <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-6 py-3 text-sm font-semibold text-emerald-100">
                                    Sudah ada di watchlist
                                </div>
                            @else
                                <form action="{{ route('watchlist.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="anime_id" value="{{ $anime->id }}">
                                    <button type="submit" class="rounded-2xl border border-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/5">
                                        Simpan ke Watchlist
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/5">
                                Login untuk Watchlist
                            </a>
                        @endauth
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Studio</p>
                            <p class="mt-3 text-base font-semibold text-white">{{ $anime->studio ?: '-' }}</p>
                        </div>
                        <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Judul Jepang</p>
                            <p class="mt-3 text-base font-semibold text-white">{{ $anime->japanese ?: '-' }}</p>
                        </div>
                        <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Producer</p>
                            <p class="mt-3 text-base font-semibold text-white">{{ $anime->producer ?: '-' }}</p>
                        </div>
                        <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Durasi</p>
                            <p class="mt-3 text-base font-semibold text-white">{{ $anime->duration ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-12 grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <div class="brand-frame glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="section-kicker text-xs">Episodes</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-white">Daftar episode</h2>
                </div>
                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300">{{ $anime->episodes->count() }} episode</span>
            </div>

            <div class="space-y-4">
                @forelse ($anime->episodes as $episode)
                    <a href="{{ route('watch.show', ['animeId' => $anime->route_id, 'episodeId' => $episode->route_id]) }}" class="group flex flex-col gap-4 rounded-[28px] border border-white/10 bg-white/5 p-4 transition hover:border-ember-500/30 hover:bg-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-[0.35em] text-ember-200">Episode {{ $episode->episode_number }}</p>
                            <h3 class="mt-2 font-display text-2xl font-bold text-white">{{ $episode->title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-400">{{ \Illuminate\Support\Str::limit($episode->synopsis, 120) }}</p>
                        </div>
                        <div class="shrink-0 text-sm text-slate-500">
                            {{ $episode->uploaded_on ?: 'Segera' }}
                        </div>
                    </a>
                @empty
                    <x-empty-state title="Belum ada episode" message="Episode untuk anime ini belum dipublikasikan." />
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="brand-frame glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <p class="section-kicker text-xs">Meta Info</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-white">Detail tambahan</h2>
                <div class="mt-6 grid gap-3">
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <span>Tipe</span>
                        <span class="font-semibold text-white">{{ $anime->type ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <span>Rilis</span>
                        <span class="font-semibold text-white">{{ $anime->release_date ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <span>Status</span>
                        <span class="font-semibold capitalize text-white">{{ $anime->status ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <span>Total Episode</span>
                        <span class="font-semibold text-white">{{ $anime->total_episode ?: $anime->episodes->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="brand-frame glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="section-kicker text-xs">Recommended</p>
                        <h2 class="mt-2 font-display text-3xl font-bold text-white">Anime serupa</h2>
                    </div>
                    <span class="text-sm text-slate-500">{{ $relatedAnimes->count() }} judul</span>
                </div>
                <div class="space-y-4">
                    @foreach ($relatedAnimes as $relatedAnime)
                        <a href="{{ route('anime.show', ['animeId' => $relatedAnime->route_id]) }}" class="flex items-center gap-4 rounded-[28px] border border-white/10 bg-white/5 p-4 transition hover:border-ember-500/30">
                            <img src="{{ $relatedAnime->poster_image }}" alt="{{ $relatedAnime->title }}" data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image" class="h-24 w-20 rounded-2xl object-cover">
                            <div class="min-w-0">
                                <h3 class="font-display text-xl font-bold text-white">{{ $relatedAnime->title }}</h3>
                                <p class="mt-1 text-sm text-slate-400">{{ $relatedAnime->studio ?: 'Studio belum tersedia' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
