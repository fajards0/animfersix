@extends('layouts.app')

@section('title', 'Fer6origami | Katalog Anime')

@section('content')
    <section class="mb-8">
        <div class="brand-frame glass-panel overflow-hidden rounded-[36px] border border-white/10 p-6 shadow-glow md:p-8">
            <div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
                <div>
                    <p class="section-kicker text-xs">Fer6origami Catalog</p>
                    <h1 class="mt-3 max-w-3xl font-display text-4xl font-bold text-white md:text-5xl">Temukan lebih banyak anime dengan filter yang lebih jelas dan tampilan yang lebih rapi.</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-400 md:text-base">
                        Katalog ini mengambil data dari daftar anime penuh, jadi sekarang kamu bisa menjelajah jauh lebih banyak judul tanpa harus bergantung pada blok homepage saja.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                    <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                        <p class="section-kicker text-[11px]">Total Catalog</p>
                        <p class="mt-3 font-display text-3xl font-bold text-white">{{ number_format($catalogTotal) }}+</p>
                        <p class="mt-1 text-sm text-slate-400">judul tersedia</p>
                    </div>
                    <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                        <p class="section-kicker text-[11px]">This Page</p>
                        <p class="mt-3 font-display text-3xl font-bold text-white">{{ $animes->count() }}</p>
                        <p class="mt-1 text-sm text-slate-400">anime pada halaman ini</p>
                    </div>
                    <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                        <p class="section-kicker text-[11px]">Current Page</p>
                        <p class="mt-3 font-display text-3xl font-bold text-white">{{ $pagination->current_page }}</p>
                        <p class="mt-1 text-sm text-slate-400">dari {{ $pagination->paginator->lastPage() }} halaman</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[340px_1fr]">
        <aside class="brand-frame glass-panel h-fit rounded-[32px] border border-white/10 p-6 shadow-glow">
            <p class="section-kicker text-xs">Anime Filter</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-white">Atur pencarian</h2>
            <form action="{{ route('anime.index') }}" method="GET" class="mt-6 space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Keyword</label>
                    <input type="text" name="search" value="{{ $selectedFilters['search'] ?? '' }}" placeholder="Judul atau studio" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-500 focus:border-ember-500 focus:ring-ember-500/30">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Genre</label>
                    <select name="genre" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                        <option value="">Semua Genre</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}" @selected(($selectedFilters['genre'] ?? '') === $genre->id)>{{ $genre->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-300">Tahun</label>
                        <select name="year" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                            <option value="">Semua</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" @selected((string) ($selectedFilters['year'] ?? '') === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-300">Status</label>
                        <select name="status" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                            <option value="">Semua</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(($selectedFilters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Skor Minimum</label>
                    <select name="rating" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                        <option value="">Semua Skor</option>
                        @foreach ($ratings as $value => $label)
                            <option value="{{ $value }}" @selected(($selectedFilters['rating'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" class="rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                        Terapkan
                    </button>
                    <a href="{{ route('anime.index') }}" class="rounded-2xl border border-white/10 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/5">
                        Reset
                    </a>
                </div>
            </form>
        </aside>

        <div>
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="section-kicker text-xs">Explore</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-white">Daftar anime</h2>
                    <p class="mt-2 text-sm text-slate-400">
                        Menampilkan {{ $animes->count() }} anime per halaman dari total {{ number_format($catalogTotal) }} judul.
                        Dengan 48 item per halaman, katalog penuh saat ini terbagi menjadi {{ $pagination->paginator->lastPage() }} halaman.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('anime.index', ['status' => 'ongoing']) }}" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Ongoing</a>
                    <a href="{{ route('anime.index', ['status' => 'completed']) }}" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Complete</a>
                    <a href="{{ route('anime.index') }}" class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Semua</a>
                </div>
            </div>

            @if ($animes->count())
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($animes as $anime)
                        <x-anime-card :anime="$anime" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $pagination->paginator->links() }}
                </div>

                @if ($pagination->previous_page || $pagination->next_page)
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                        @if ($pagination->previous_page)
                            <a
                                href="{{ route('anime.index', array_merge(request()->query(), ['page' => $pagination->previous_page])) }}"
                                class="rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                            >
                                Halaman Sebelumnya
                            </a>
                        @endif

                        <span class="rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-sm font-semibold text-slate-300">
                            Page {{ $pagination->current_page }}
                        </span>

                        @if ($pagination->next_page)
                            <a
                                href="{{ route('anime.index', array_merge(request()->query(), ['page' => $pagination->next_page])) }}"
                                class="rounded-2xl border border-ember-500/30 bg-ember-500/10 px-5 py-3 text-sm font-semibold text-ember-300 transition hover:bg-ember-500/20"
                            >
                                Halaman Berikutnya
                            </a>
                        @endif
                    </div>
                @endif
            @else
                <x-empty-state
                    title="Anime tidak ditemukan"
                    message="Coba ubah keyword pencarian atau longgarkan filter genre, tahun, status, dan skor minimum untuk melihat hasil lain."
                />
            @endif
        </div>
    </section>
@endsection
