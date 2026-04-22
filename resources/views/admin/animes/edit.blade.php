@extends('layouts.app')

@section('title', 'Fer6origami | Edit Anime')

@section('content')
    <section class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
        <h1 class="font-display text-4xl font-bold text-white">Edit anime</h1>
        <p class="mt-2 text-sm text-slate-400">Perbarui data {{ $anime->title }}.</p>

        <form action="{{ route('admin.animes.update', $anime) }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @method('PUT')
            @include('admin.animes._form')
        </form>
    </section>
@endsection
