@props(['anime'])

<article class="group brand-frame overflow-hidden rounded-[30px] border border-white/10 bg-white/5 shadow-card transition duration-300 hover:-translate-y-1.5 hover:border-ember-500/40 hover:bg-white/10">
    <a href="{{ route('anime.show', ['animeId' => $anime->route_id]) }}" class="block">
        <div class="relative aspect-[4/5] overflow-hidden">
            <img src="{{ $anime->poster_image }}" alt="{{ $anime->title }}" data-fallback-src="https://placehold.co/600x900/09090b/f97316?text=No+Image" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-coal-950 via-coal-950/15 to-transparent"></div>

            <div class="absolute inset-x-0 top-0 flex items-start justify-between gap-3 p-4">
                <span class="rounded-full border border-white/10 bg-coal-950/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-200">
                    {{ $anime->type ?: 'Anime' }}
                </span>
                <span class="rounded-full border border-amber-300/20 bg-amber-300/10 px-3 py-1 text-sm font-bold text-amber-200">
                    {{ number_format((float) $anime->score, 1) }}
                </span>
            </div>

            <div class="absolute inset-x-0 bottom-0 p-4">
                <div class="flex flex-wrap gap-2">
                    @foreach ($anime->genres->take(2) as $genre)
                        <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-[11px] font-semibold text-white">
                            {{ $genre->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-4 p-5">
            <div class="min-w-0">
                <h3 class="line-clamp-2 font-display text-2xl font-bold text-white">{{ $anime->title }}</h3>
                <p class="mt-2 text-sm leading-6 text-slate-400">{{ \Illuminate\Support\Str::limit($anime->synopsis, 95) }}</p>
            </div>

            <div class="flex items-center justify-between gap-3 rounded-[22px] border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                <span>{{ $anime->year ?? 'TBA' }}</span>
                <span class="truncate">{{ $anime->episode_label ?: ($anime->status ?: 'Anime') }}</span>
            </div>
        </div>
    </a>
</article>
