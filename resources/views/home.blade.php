@extends('layouts.app')

@section('title', 'Fer6origami | Home')

@section('content')
    @php($hero = $banners->first())
    @php($spotlightAnime = $popularAnimes->first() ?? $trendingAnimes->first())

    <section class="space-y-10">
        {{-- HERO --}}
        <section class="overflow-hidden rounded-[36px] border border-white/10 bg-[#0b1020] shadow-[0_20px_80px_rgba(0,0,0,0.35)]">
            <div class="relative min-h-[620px]">
                <img
                    src="{{ $hero?->image_url ?? ($spotlightAnime?->banner_image ?? 'https://placehold.co/1600x900/101317/ff6b3d?text=Fer6origami') }}"
                    alt="{{ $hero?->title ?? 'Fer6origami Hero' }}"
                    data-fallback-src="https://placehold.co/1600x900/101317/ff6b3d?text=Fer6origami"
                    class="absolute inset-0 h-full w-full object-cover opacity-25"
                >
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950/85 to-orange-900/30"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,120,80,0.18),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(99,102,241,0.14),transparent_28%)]"></div>

                <div class="relative grid min-h-[620px] gap-8 px-6 py-8 md:px-10 md:py-10 xl:grid-cols-[1.2fr_.8fr] xl:items-end">
                    <div class="flex flex-col justify-between">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.35em] text-slate-300">
                                Fer6origami
                            </span>
                            <span class="rounded-full border border-orange-400/20 bg-orange-400/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.35em] text-orange-200">
                                Anime Home
                            </span>
                        </div>

                        <div class="max-w-3xl pt-10">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-200/90">
                                Discover anime faster
                            </p>

                            <h1 class="mt-4 text-4xl font-black tracking-tight text-white md:text-6xl md:leading-[1.05]">
                                {{ $hero?->title ?? 'Fer6origami adalah aplikasi untuk menonton anime secara gratis, tanpa iklan, dengan tampilan yang lebih bersih dan nyaman.' }}
                            </h1>

                            <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-300 md:text-base">
                                {{ $hero?->subtitle ?? 'Lihat judul trending, update episode terbaru, genre populer, dan akses cepat ke katalog dalam satu halaman yang lebih bersih dan fokus.' }}
                            </p>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="{{ $hero?->button_link ?? route('anime.index') }}"
                                   class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-slate-950 transition hover:scale-[1.02] hover:bg-slate-100">
                                    {{ $hero?->button_text ?? 'Buka Katalog' }}
                                </a>

                                <a href="{{ route('anime.index', ['status' => 'ongoing']) }}"
                                   class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                    Lihat Ongoing
                                </a>
                            </div>

                            <div class="mt-8 grid max-w-2xl gap-3 sm:grid-cols-3">
                                <div class="rounded-[24px] border border-white/10 bg-white/5 px-5 py-4 backdrop-blur-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">Catalog</p>
                                    <p class="mt-2 text-3xl font-black text-white">{{ number_format($catalogTotal) }}+</p>
                                    <p class="mt-1 text-sm text-slate-400">anime tersedia</p>
                                </div>
                                <div class="rounded-[24px] border border-white/10 bg-white/5 px-5 py-4 backdrop-blur-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">Trending</p>
                                    <p class="mt-2 text-3xl font-black text-white">{{ $trendingAnimes->count() }}</p>
                                    <p class="mt-1 text-sm text-slate-400">judul ramai</p>
                                </div>
                                <div class="rounded-[24px] border border-white/10 bg-white/5 px-5 py-4 backdrop-blur-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">Fresh Drop</p>
                                    <p class="mt-2 text-3xl font-black text-white">{{ $latestEpisodes->count() }}</p>
                                    <p class="mt-1 text-sm text-slate-400">episode baru</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- HERO SIDE PANEL --}}
                    <div class="grid gap-5 xl:pl-6">
                        <div class="rounded-[28px] border border-white/10 bg-slate-950/45 p-6 backdrop-blur-md">
                            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">Quick Search</p>
                            <h2 class="mt-2 text-2xl font-black text-white">Cari anime cepat</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Langsung cari judul, pilih genre, atau filter status.</p>

                            <form action="{{ route('anime.index') }}" method="GET" class="mt-5 space-y-4">
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Cari judul anime..."
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-400/10"
                                >

                                <div class="grid grid-cols-2 gap-3">
                                    <select name="genre"
                                            class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-400/10">
                                        <option value="">Semua Genre</option>
                                        @foreach ($filters['genres'] as $genre)
                                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                                        @endforeach
                                    </select>

                                    <select name="status"
                                            class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-400/10">
                                        <option value="">Semua Status</option>
                                        @foreach ($filters['statuses'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit"
                                        class="w-full rounded-2xl bg-gradient-to-r from-orange-500 to-amber-400 px-4 py-3 text-sm font-bold text-white transition hover:opacity-95">
                                    Jelajahi Sekarang
                                </button>
                            </form>
                        </div>

                        @if ($spotlightAnime)
                            <a href="{{ route('anime.show', ['animeId' => $spotlightAnime->route_id]) }}"
                               class="group overflow-hidden rounded-[28px] border border-white/10 bg-white/5 transition hover:border-orange-400/30 hover:bg-white/[0.07]">
                                <div class="relative h-52 overflow-hidden">
                                    <img
                                        src="{{ $spotlightAnime->banner_image }}"
                                        alt="{{ $spotlightAnime->title }}"
                                        data-fallback-src="https://placehold.co/1400x700/111827/f43f5e?text=No+Banner"
                                        onerror="this.onerror=null;this.src=this.dataset.fallbackSrc;"
                                        class="h-full w-full object-cover opacity-75 transition duration-500 group-hover:scale-105"
                                    >
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent"></div>
                                    <div class="absolute inset-x-0 bottom-0 p-5">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-orange-200">Spotlight</p>
                                        <h3 class="mt-2 text-2xl font-black text-white">{{ $spotlightAnime->title }}</h3>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <p class="text-sm leading-7 text-slate-400">
                                        {{ \Illuminate\Support\Str::limit($spotlightAnime->synopsis, 120) }}
                                    </p>
                                    <div class="mt-4 flex items-center justify-between text-sm text-slate-300">
                                        <span>{{ $spotlightAnime->studio ?: 'Studio belum tersedia' }}</span>
                                        <span>{{ number_format((float) $spotlightAnime->score, 1) }} ★</span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- GENRE --}}
        <section class="rounded-[30px] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
            <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">Browse by Genre</p>
                    <h2 class="mt-2 text-3xl font-black text-white">Masuk dari genre yang kamu suka</h2>
                </div>
                <a href="{{ route('anime.index') }}" class="text-sm font-semibold text-orange-200 hover:text-orange-100">
                    Katalog penuh
                </a>
            </div>

            <div class="flex flex-wrap gap-3">
                @foreach ($filters['genres']->take(14) as $genre)
                    <a href="{{ route('anime.index', ['genre' => $genre->id]) }}"
                       class="rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm font-semibold text-slate-200 transition hover:border-orange-400/30 hover:bg-slate-800">
                        {{ $genre->name }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- TRENDING --}}
        <section>
            <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">Trending Now</p>
                    <h2 class="mt-2 text-3xl font-black text-white">Judul yang sedang ramai</h2>
                </div>
                <a href="{{ route('anime.index', ['status' => 'ongoing']) }}" class="text-sm font-semibold text-orange-200 hover:text-orange-100">
                    Lihat ongoing
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($trendingAnimes as $anime)
                    <x-anime-card :anime="$anime" />
                @endforeach
            </div>
        </section>

        {{-- LATEST + SIDE --}}
        <section class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
            <div class="rounded-[30px] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">Latest Episode</p>
                        <h2 class="mt-2 text-3xl font-black text-white">Update episode terbaru</h2>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300">
                        {{ $latestEpisodes->count() }} item
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach ($latestEpisodes as $episode)
                        <a href="{{ route('watch.show', ['animeId' => $episode->anime_route_id, 'episodeId' => $episode->route_id]) }}"
                           class="group flex flex-col gap-4 rounded-[24px] border border-white/10 bg-slate-900/55 p-4 transition hover:border-orange-400/30 hover:bg-slate-900/80 sm:flex-row">
                            <img
                                src="{{ $episode->anime->poster_image }}"
                                alt="{{ $episode->anime->title }}"
                                data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image"
                                class="h-32 w-full rounded-2xl object-cover sm:w-24"
                            >
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-orange-200">
                                        Episode {{ $episode->episode_number }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $episode->uploaded_on ?: '-' }}</p>
                                </div>
                                <h3 class="mt-3 text-2xl font-black text-white">{{ $episode->anime->title }}</h3>
                                <p class="mt-1 text-sm text-slate-300">{{ $episode->title }}</p>
                                <p class="mt-3 text-sm leading-6 text-slate-400">
                                    {{ \Illuminate\Support\Str::limit($episode->synopsis, 110) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-6">
                <div class="rounded-[30px] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">Catalog Drop</p>
                            <h2 class="mt-2 text-3xl font-black text-white">Lebih banyak anime</h2>
                        </div>
                        <a href="{{ route('anime.index') }}" class="text-sm font-semibold text-orange-200 hover:text-orange-100">
                            Buka katalog
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($catalogHighlights->take(6) as $anime)
                            <a href="{{ route('anime.show', ['animeId' => $anime->route_id]) }}"
                               class="flex items-center gap-4 rounded-[22px] border border-white/10 bg-slate-900/55 p-3 transition hover:border-orange-400/30">
                                <img src="{{ $anime->poster_image }}" alt="{{ $anime->title }}"
                                     data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image"
                                     class="h-20 w-16 rounded-xl object-cover">
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-lg font-bold text-white">{{ $anime->title }}</h3>
                                    <p class="mt-1 text-sm text-slate-400">{{ $anime->year ?: 'TBA' }} • {{ $anime->status ?: 'Anime' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-1">
                    <div class="rounded-[30px] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                        <div class="mb-5 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">Ongoing Picks</p>
                                <h2 class="mt-2 text-2xl font-black text-white">Sedang berjalan</h2>
                            </div>
                            <a href="{{ route('anime.index', ['status' => 'ongoing']) }}" class="text-sm font-semibold text-orange-200">
                                Lihat semua
                            </a>
                        </div>

                        <div class="grid gap-3">
                            @foreach ($ongoingAnimes->take(4) as $anime)
                                <a href="{{ route('anime.show', ['animeId' => $anime->route_id]) }}"
                                   class="flex items-center gap-4 rounded-[22px] border border-white/10 bg-slate-900/55 p-3 transition hover:border-orange-400/30">
                                    <img src="{{ $anime->poster_image }}" alt="{{ $anime->title }}"
                                         data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image"
                                         class="h-20 w-16 rounded-xl object-cover">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="truncate text-lg font-bold text-white">{{ $anime->title }}</h3>
                                        <p class="mt-1 text-sm text-slate-400">{{ $anime->episode_label ?: 'Episode update tersedia' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-[30px] border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                        <div class="mb-5 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">Complete Picks</p>
                                <h2 class="mt-2 text-2xl font-black text-white">Seri tamat</h2>
                            </div>
                            <a href="{{ route('anime.index', ['status' => 'completed']) }}" class="text-sm font-semibold text-orange-200">
                                Lihat semua
                            </a>
                        </div>

                        <div class="grid gap-3">
                            @foreach ($completeAnimes->take(4) as $anime)
                                <a href="{{ route('anime.show', ['animeId' => $anime->route_id]) }}"
                                   class="flex items-center gap-4 rounded-[22px] border border-white/10 bg-slate-900/55 p-3 transition hover:border-orange-400/30">
                                    <img src="{{ $anime->poster_image }}" alt="{{ $anime->title }}"
                                         data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image"
                                         class="h-20 w-16 rounded-xl object-cover">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="truncate text-lg font-bold text-white">{{ $anime->title }}</h3>
                                        <p class="mt-1 text-sm text-slate-400">{{ $anime->studio ?: 'Studio belum tersedia' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection