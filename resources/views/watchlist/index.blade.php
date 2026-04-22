@extends('layouts.app')

@section('title', 'Fer6origami | Watchlist')

@section('content')
    <section class="mb-8">
        <div class="brand-frame glass-panel rounded-[36px] border border-white/10 p-6 shadow-glow md:p-8">
            <div class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
                <div>
                    <p class="section-kicker text-xs">Personal Library</p>
                    <h1 class="mt-3 font-display text-4xl font-bold text-white md:text-5xl">Watchlist kamu, disusun buat lanjut nonton tanpa ribet.</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-400">
                        Semua anime yang sudah kamu simpan dikumpulkan di satu tempat, jadi kamu bisa lanjut nonton atau bersih-bersih list kapan saja.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                    <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                        <p class="section-kicker text-[11px]">Saved</p>
                        <p class="mt-3 font-display text-3xl font-bold text-white">{{ $watchlists->total() }}</p>
                        <p class="mt-1 text-sm text-slate-400">anime tersimpan</p>
                    </div>
                    <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                        <p class="section-kicker text-[11px]">Page</p>
                        <p class="mt-3 font-display text-3xl font-bold text-white">{{ $watchlists->currentPage() }}</p>
                        <p class="mt-1 text-sm text-slate-400">halaman aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        @if ($watchlists->count())
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($watchlists as $watchlist)
                    <div class="space-y-4">
                        <x-anime-card :anime="$watchlist->toAnimeCard()" />
                        <form action="{{ route('watchlist.destroy', $watchlist) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                Hapus dari Watchlist
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $watchlists->links() }}
            </div>
        @else
            <x-empty-state
                title="Watchlist masih kosong"
                message="Simpan anime favorit dari halaman detail anime, lalu semuanya akan muncul di sini."
            />
        @endif
    </section>
@endsection
