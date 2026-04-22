@extends('layouts.app')

@section('title', 'Fer6origami | Nonton ' . $episode->anime->title)

@section('content')
    <section class="grid gap-6 xl:grid-cols-[1.25fr_.75fr]">
        <div class="space-y-6">
            <div class="brand-frame overflow-hidden rounded-[32px] border border-white/10 bg-slate-950 shadow-glow">
                @php($isDirectVideo = preg_match('/\.(mp4|m3u8|webm)(\?.*)?$/i', $episode->stream_url))

                @if ($isDirectVideo)
                    <video controls preload="metadata" class="aspect-video w-full bg-black" poster="{{ $episode->anime->banner_image }}">
                        <source src="{{ $episode->stream_url }}" type="video/mp4">
                        Browser Anda belum mendukung video HTML5.
                    </video>
                @else
                    <iframe
                        src="{{ $episode->stream_url }}"
                        class="aspect-video w-full bg-black"
                        allowfullscreen
                        referrerpolicy="strict-origin-when-cross-origin"
                    ></iframe>
                @endif
            </div>

            <div class="brand-frame glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full border border-ember-500/30 bg-ember-500/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-ember-200">Now Playing</span>
                    @if ($episode->stream_quality)
                        <span class="rounded-full border border-emerald-400/30 bg-emerald-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-emerald-100">
                            Streaming {{ $episode->stream_quality }}
                        </span>
                    @endif
                </div>

                <h1 class="mt-5 font-display text-4xl font-bold text-white">{{ $episode->anime->title }}</h1>
                <p class="mt-2 text-sm font-semibold text-slate-300">Episode {{ $episode->episode_number }} - {{ $episode->title }}</p>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-400">{{ $episode->synopsis ?: 'Episode ini menggunakan streaming URL dari API Otakudesu lokal.' }}</p>

                @if ($episode->stream_options->count())
                    <div class="mt-6 rounded-[24px] border border-white/10 bg-white/5 p-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs uppercase tracking-[0.3em] text-slate-500">Streaming Quality</span>
                            @foreach ($episode->stream_options as $option)
                                <a
                                    href="{{ route('watch.show', ['animeId' => $episode->anime->route_id, 'episodeId' => $episode->route_id, 'quality' => $option['quality']]) }}"
                                    class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] transition {{ ($option['quality'] ?? null) === $episode->stream_quality ? 'border-emerald-400/40 bg-emerald-400/10 text-emerald-100' : 'border-white/10 bg-slate-950/40 text-slate-200 hover:bg-white/5' }}"
                                >
                                    {{ $option['quality'] ?? '-' }}
                                    @if (!empty($option['label']))
                                        <span class="text-[10px] text-slate-400">{{ $option['label'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($episode->qualities->count())
                    <div class="mt-6 rounded-[24px] border border-white/10 bg-white/5 p-4">
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            <span class="text-xs uppercase tracking-[0.3em] text-slate-500">Download Quality</span>
                            @foreach ($episode->qualities as $quality)
                                <span class="rounded-full border border-ember-500/30 bg-ember-500/10 px-3 py-1 text-xs font-semibold text-ember-100">
                                    {{ $quality['quality'] ?? '-' }}
                                </span>
                            @endforeach
                        </div>

                        <div class="grid gap-3">
                            @foreach ($episode->qualities as $quality)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="font-semibold text-white">{{ $quality['quality'] ?? '-' }}</span>
                                        @if (!empty($quality['size']))
                                            <span class="text-sm text-slate-400">{{ $quality['size'] }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach (($quality['download_links'] ?? []) as $link)
                                            <a href="{{ $link['link'] }}" target="_blank" rel="noreferrer" class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/5">
                                                {{ $link['host'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($previousEpisode)
                        <a href="{{ route('watch.show', ['animeId' => $episode->anime->route_id, 'episodeId' => $previousEpisode->route_id]) }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">
                            Episode Sebelumnya
                        </a>
                    @endif
                    @if ($nextEpisode)
                        <a href="{{ route('watch.show', ['animeId' => $episode->anime->route_id, 'episodeId' => $nextEpisode->route_id]) }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                            Episode Berikutnya
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="brand-frame glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="flex items-center gap-4">
                    <img src="{{ $episode->anime->poster_image }}" alt="{{ $episode->anime->title }}" data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image" class="h-28 w-20 rounded-[24px] object-cover">
                    <div class="min-w-0">
                        <p class="section-kicker text-xs">Series</p>
                        <h2 class="mt-2 font-display text-2xl font-bold text-white">{{ $episode->anime->title }}</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ $episode->anime->studio ?: 'Studio belum tersedia' }}</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3">
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <span>Episode aktif</span>
                        <span class="font-semibold text-white">{{ $episode->episode_number }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <span>Tahun</span>
                        <span class="font-semibold text-white">{{ $episode->anime->year ?: 'TBA' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                        <span>Status</span>
                        <span class="font-semibold text-white capitalize">{{ $episode->anime->status ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="brand-frame glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="section-kicker text-xs">Episode List</p>
                        <h2 class="mt-2 font-display text-3xl font-bold text-white">Semua episode</h2>
                    </div>
                    <span class="text-sm text-slate-500">{{ $episode->anime->episodes->count() }} total</span>
                </div>

                <div class="space-y-3">
                    @foreach ($episode->anime->episodes as $item)
                        <a href="{{ route('watch.show', ['animeId' => $episode->anime->route_id, 'episodeId' => $item->route_id]) }}" class="block rounded-[24px] border px-4 py-4 transition {{ $item->id === $episode->id ? 'border-ember-500/40 bg-ember-500/10 text-white' : 'border-white/10 bg-white/5 text-slate-300 hover:border-white/20 hover:bg-white/10' }}">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs uppercase tracking-[0.35em] {{ $item->id === $episode->id ? 'text-ember-200' : 'text-slate-500' }}">Episode {{ $item->episode_number }}</p>
                                    <p class="mt-2 font-semibold">{{ $item->title }}</p>
                                </div>
                                <span class="text-xs text-slate-500">{{ $item->uploaded_on ?: '-' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="brand-frame glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="mb-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="section-kicker text-xs">More Like This</p>
                        <h2 class="mt-2 font-display text-3xl font-bold text-white">Rekomendasi</h2>
                    </div>
                    <span class="text-sm text-slate-500">{{ $recommendations->count() }} judul</span>
                </div>

                <div class="grid gap-4">
                    @foreach ($recommendations as $anime)
                        <a href="{{ route('anime.show', ['animeId' => $anime->route_id]) }}" class="flex items-center gap-4 rounded-[28px] border border-white/10 bg-white/5 p-4 transition hover:border-ember-500/30">
                            <img src="{{ $anime->poster_image }}" alt="{{ $anime->title }}" data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image" class="h-20 w-16 rounded-2xl object-cover">
                            <div class="min-w-0">
                                <h3 class="font-display text-xl font-bold text-white">{{ $anime->title }}</h3>
                                <p class="mt-1 text-sm text-slate-400">{{ $anime->studio ?: 'Studio belum tersedia' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
