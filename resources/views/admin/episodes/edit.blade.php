@extends('layouts.app')

@section('title', 'Fer6origami | Edit Episode')

@section('content')
    <section class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
        <h1 class="font-display text-4xl font-bold text-white">Edit episode</h1>
        <p class="mt-2 text-sm text-slate-400">Perbarui detail episode {{ $episode->title }}.</p>

        <form action="{{ route('admin.episodes.update', $episode) }}" method="POST" class="mt-8">
            @method('PUT')
            @include('admin.episodes._form')
        </form>
    </section>
@endsection
