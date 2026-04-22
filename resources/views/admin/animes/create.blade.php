@extends('layouts.app')

@section('title', 'Fer6origami | Tambah Anime')

@section('content')
    <section class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
        <h1 class="font-display text-4xl font-bold text-white">Tambah anime</h1>
        <p class="mt-2 text-sm text-slate-400">Masukkan metadata anime, genre, dan aset poster/banner.</p>

        <form action="{{ route('admin.animes.store') }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @include('admin.animes._form')
        </form>
    </section>
@endsection
