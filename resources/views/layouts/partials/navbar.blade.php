<header class="relative z-20">
    <div class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
        <div class="brand-frame glass-panel rounded-[28px] border border-white/10 px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[18px] bg-gradient-to-br from-ember-400 via-ember-500 to-tide-400 font-display text-lg font-extrabold text-white shadow-card">
                        F6
                    </span>
                    <div class="min-w-0">
                        <p class="truncate font-display text-xl font-bold tracking-tight text-white">Fer6origami</p>
                        <p class="truncate text-[11px] uppercase tracking-[0.38em] text-slate-400">Anime Catalog Atelier</p>
                    </div>
                </a>

                <nav class="hidden items-center gap-6 text-sm font-semibold text-slate-300 xl:flex">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-white' : 'hover:text-white' }}">Home</a>
                    <a href="{{ route('anime.index') }}" class="{{ request()->routeIs('anime.*') ? 'text-white' : 'hover:text-white' }}">Katalog</a>
                    <a href="{{ route('anime.index', ['status' => 'ongoing']) }}" class="transition hover:text-white">Ongoing</a>
                    <a href="{{ route('anime.index', ['status' => 'completed']) }}" class="transition hover:text-white">Complete</a>
                    <button
                        type="button"
                        data-menu-toggle="genre-panel"
                        class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10"
                    >
                        Genre
                    </button>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    <form action="{{ route('anime.index') }}" method="GET" class="w-72">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari anime, genre, studio..."
                            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-ember-500 focus:ring-ember-500/30"
                        >
                    </form>

                    @auth
                        <a href="{{ route('watchlist.index') }}" class="rounded-2xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-white/20 hover:bg-white/5">
                            Watchlist
                        </a>

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-2xl border border-ember-500/40 bg-ember-500/10 px-4 py-2 text-sm font-semibold text-ember-200 transition hover:border-ember-400 hover:bg-ember-500/20">
                                Admin
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-white/20 hover:bg-white/5">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                            Join
                        </a>
                    @endauth
                </div>

                <button
                    type="button"
                    data-menu-toggle="mobile-nav"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-xl text-white lg:hidden"
                    aria-label="Buka menu"
                >
                    &#9776;
                </button>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-white/10 pt-4">
                <span class="rounded-full bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">
                    {{ ($navGenres ?? collect())->count() }} genre tersedia
                </span>
                <span class="rounded-full bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">
                    Lebih banyak anime
                </span>
                <span class="rounded-full bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">
                    Fokus katalog
                </span>
            </div>
        </div>
    </div>

    <div id="genre-panel" class="mx-auto hidden w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="brand-frame glass-panel mt-3 rounded-[24px] border border-white/10 p-5">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="section-kicker text-xs">Genre Explorer</p>
                    <h2 class="mt-2 font-display text-2xl font-bold text-white">Pilih genre langsung dari navbar</h2>
                </div>
                <a href="{{ route('anime.index') }}" class="text-sm font-semibold text-ember-200">Buka katalog penuh</a>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach (($navGenres ?? collect()) as $genre)
                    <a href="{{ route('anime.index', ['genre' => $genre->id]) }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-slate-200 transition hover:border-ember-500/30 hover:bg-white/10">
                        {{ $genre->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div id="mobile-nav" class="mx-auto hidden w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="brand-frame glass-panel mt-3 rounded-[24px] border border-white/10 p-4 lg:hidden">
            <div class="space-y-4">
                <form action="{{ route('anime.index') }}" method="GET">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari anime..."
                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-ember-500 focus:ring-ember-500/30"
                    >
                </form>

                <div class="grid gap-2 text-sm text-slate-300">
                    <a href="{{ route('home') }}" class="rounded-2xl border border-white/10 px-4 py-3">Home</a>
                    <a href="{{ route('anime.index') }}" class="rounded-2xl border border-white/10 px-4 py-3">Katalog</a>
                    <a href="{{ route('anime.index', ['status' => 'ongoing']) }}" class="rounded-2xl border border-white/10 px-4 py-3">Ongoing</a>
                    <a href="{{ route('anime.index', ['status' => 'completed']) }}" class="rounded-2xl border border-white/10 px-4 py-3">Complete</a>
                </div>

                <div class="border-t border-white/10 pt-4">
                    <p class="mb-3 text-xs uppercase tracking-[0.35em] text-slate-500">Genre</p>
                    <div class="grid grid-cols-2 gap-2 text-sm text-slate-300">
                        @foreach (($navGenres ?? collect()) as $genre)
                            <a href="{{ route('anime.index', ['genre' => $genre->id]) }}" class="rounded-2xl border border-white/10 px-4 py-3 transition hover:bg-white/5">
                                {{ $genre->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-2 text-sm text-slate-300 border-t border-white/10 pt-4">
                    @auth
                        <a href="{{ route('watchlist.index') }}" class="rounded-2xl border border-white/10 px-4 py-3">Watchlist</a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-2xl border border-white/10 px-4 py-3">Admin</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl bg-white px-4 py-3 text-left font-semibold text-slate-950">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 px-4 py-3">Login</a>
                        <a href="{{ route('register') }}" class="rounded-2xl bg-white px-4 py-3 font-semibold text-slate-950">Join</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>
