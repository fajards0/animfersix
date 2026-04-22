@extends('layouts.app')

@section('title', 'Fer6origami | Login')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[1fr_.9fr]">
        <div class="brand-frame glass-panel overflow-hidden rounded-[36px] border border-white/10 p-8 shadow-glow md:p-10">
            <p class="section-kicker text-xs">Welcome Back</p>
            <h1 class="mt-3 max-w-2xl font-display text-4xl font-bold text-white md:text-5xl">Masuk lagi ke Fer6origami dan lanjutkan anime yang sudah kamu simpan.</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-400">
                Login dipakai untuk menyimpan watchlist, menata ulang anime favorit, dan masuk ke dashboard admin kalau akunmu punya akses.
            </p>

            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                    <p class="section-kicker text-[11px]">Watchlist</p>
                    <p class="mt-2 text-sm text-slate-300">Simpan judul favorit dalam satu tempat.</p>
                </div>
                <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                    <p class="section-kicker text-[11px]">History</p>
                    <p class="mt-2 text-sm text-slate-300">Balik lagi ke anime yang terakhir kamu cek.</p>
                </div>
                <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                    <p class="section-kicker text-[11px]">Access</p>
                    <p class="mt-2 text-sm text-slate-300">Masuk ke area admin kalau role kamu tersedia.</p>
                </div>
            </div>
        </div>

        <div class="brand-frame glass-panel rounded-[36px] border border-white/10 p-8 shadow-glow">
            <p class="section-kicker text-xs">Login</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-white">Masuk ke akun</h2>

            <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                    @error('email')
                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Password</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                    @error('password')
                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-400">
                    <input type="checkbox" name="remember" value="1" class="rounded border-white/10 bg-white/5 text-ember-500 focus:ring-ember-500/30">
                    Ingat saya
                </label>

                <button type="submit" class="w-full rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                    Login
                </button>
            </form>

            <p class="mt-6 text-sm text-slate-400">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-white">Daftar sekarang</a>
            </p>
        </div>
    </section>
@endsection
