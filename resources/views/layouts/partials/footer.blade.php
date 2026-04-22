<footer class="relative z-10 mt-16">
    <div class="mx-auto w-full max-w-7xl px-4 pb-6 sm:px-6 lg:px-8">
        <div class="brand-frame glass-panel rounded-[32px] border border-white/10 px-6 py-8">
            <div class="grid gap-8 lg:grid-cols-[1.2fr_.8fr_.8fr]">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Fer6origami</p>
                    <h2 class="mt-3 max-w-xl font-display text-3xl font-bold text-white">Katalog anime yang terasa lebih editorial, lebih padat, dan lebih enak dijelajahi.</h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-400">
                        Homepage sekarang difokuskan untuk menampilkan lebih banyak judul, entry point pencarian yang cepat, dan layout yang lebih bersih di layar kecil maupun besar.
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Explore</p>
                    <div class="mt-4 grid gap-3 text-sm text-slate-300">
                        <a href="{{ route('home') }}" class="transition hover:text-white">Home</a>
                        <a href="{{ route('anime.index') }}" class="transition hover:text-white">Katalog Anime</a>
                        <a href="{{ route('anime.index', ['status' => 'ongoing']) }}" class="transition hover:text-white">Anime Ongoing</a>
                        <a href="{{ route('anime.index', ['status' => 'completed']) }}" class="transition hover:text-white">Anime Complete</a>
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Stack</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-300">Laravel</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-300">Blade</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-300">Tailwind CDN</span>
                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-300">Otakudesu Scraper</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 border-t border-white/10 pt-5 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>Fer6origami dirancang untuk katalog anime yang lebih ramai dan lebih mudah ditelusuri.</p>
                <p>Built with Laravel and a refreshed Blade UI.</p>
            </div>
        </div>
    </div>
</footer>
