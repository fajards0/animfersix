@extends('layouts.app')

@section('title', 'Fer6origami | Register')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[1fr_.9fr]">
        <div class="brand-frame glass-panel overflow-hidden rounded-[36px] border border-white/10 p-8 shadow-glow md:p-10">
            <p class="section-kicker text-xs">Create Account</p>
            <h1 class="mt-3 max-w-2xl font-display text-4xl font-bold text-white md:text-5xl">Bikin akun baru dan mulai susun katalog anime favoritmu sendiri.</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-400">
                Setelah daftar, kamu bisa simpan anime ke watchlist dan pakai Fer6origami dengan pengalaman yang lebih personal.
            </p>

            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                    <p class="section-kicker text-[11px]">Save</p>
                    <p class="mt-2 text-sm text-slate-300">Tandai anime yang ingin kamu lanjutkan nanti.</p>
                </div>
                <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                    <p class="section-kicker text-[11px]">Organize</p>
                    <p class="mt-2 text-sm text-slate-300">Rapikan daftar tontonan favoritmu sendiri.</p>
                </div>
                <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
                    <p class="section-kicker text-[11px]">Discover</p>
                    <p class="mt-2 text-sm text-slate-300">Balik lagi dengan akses yang lebih personal.</p>
                </div>
            </div>
        </div>

        <div class="brand-frame glass-panel rounded-[36px] border border-white/10 p-8 shadow-glow">
            <p class="section-kicker text-xs">Register</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-white">Buat akun baru</h2>

            <form action="{{ route('register') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                    @error('name')
                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

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

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-ember-500 focus:ring-ember-500/30">
                </div>

                <button type="submit" class="w-full rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                    Register
                </button>
            </form>

            <p class="mt-6 text-sm text-slate-400">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-white">Login di sini</a>
            </p>
        </div>
    </section>
@endsection
