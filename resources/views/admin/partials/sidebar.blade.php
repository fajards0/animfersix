<aside class="hidden w-72 shrink-0 lg:block">
    <div class="glass-panel sticky top-28 rounded-[28px] border border-white/10 p-5 shadow-glow">
        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin Panel</p>
        <h2 class="mt-2 font-display text-2xl font-bold text-white">Control Room</h2>

        <nav class="mt-6 space-y-2 text-sm">
            @php
                $links = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                    ['route' => 'admin.animes.index', 'label' => 'Kelola Anime'],
                    ['route' => 'admin.episodes.index', 'label' => 'Kelola Episode'],
                    ['route' => 'admin.genres.index', 'label' => 'Kelola Genre'],
                    ['route' => 'admin.banners.index', 'label' => 'Kelola Banner'],
                    ['route' => 'admin.users.index', 'label' => 'Kelola User'],
                ];
            @endphp

            @foreach ($links as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="block rounded-2xl px-4 py-3 transition {{ request()->routeIs(str_replace('.index', '.*', $link['route'])) || request()->routeIs($link['route']) ? 'bg-white text-slate-950' : 'border border-white/10 text-slate-300 hover:border-white/20 hover:bg-white/5 hover:text-white' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</aside>
