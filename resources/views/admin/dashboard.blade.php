@extends('layouts.app')

@section('title', 'Fer6origami | Admin Dashboard')

@section('content')
    <section>
        <div class="mb-8">
            <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin Dashboard</p>
            <h1 class="mt-2 font-display text-4xl font-bold text-white">Kelola Fer6origami</h1>
            <p class="mt-3 text-sm text-slate-400">Ringkasan cepat untuk katalog anime, episode, genre, banner, dan user.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($stats as $label => $value)
                <div class="glass-panel rounded-[28px] border border-white/10 p-5 shadow-glow">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ ucfirst($label) }}</p>
                    <p class="mt-4 font-display text-4xl font-bold text-white">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-3">
            <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="font-display text-2xl font-bold text-white">Anime terbaru</h2>
                    <a href="{{ route('admin.animes.index') }}" class="text-sm font-semibold text-ember-300">Lihat</a>
                </div>
                <div class="space-y-4">
                    @foreach ($latestAnimes as $anime)
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                            <p class="font-semibold text-white">{{ $anime->title }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ $anime->studio }} • {{ $anime->year }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="font-display text-2xl font-bold text-white">Episode terbaru</h2>
                    <a href="{{ route('admin.episodes.index') }}" class="text-sm font-semibold text-ember-300">Lihat</a>
                </div>
                <div class="space-y-4">
                    @foreach ($latestEpisodes as $episode)
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                            <p class="font-semibold text-white">{{ $episode->title }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ $episode->anime->title }} • Ep {{ $episode->episode_number }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="font-display text-2xl font-bold text-white">User terbaru</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-ember-300">Lihat</a>
                </div>
                <div class="space-y-4">
                    @foreach ($latestUsers as $user)
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                            <p class="font-semibold text-white">{{ $user->name }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ $user->email }} • {{ strtoupper($user->role) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
