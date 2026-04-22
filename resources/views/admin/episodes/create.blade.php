@extends('layouts.app')

@section('title', 'Fer6origami | Tambah Episode')

@section('content')
    <section class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
        <h1 class="font-display text-4xl font-bold text-white">Tambah episode</h1>
        <p class="mt-2 text-sm text-slate-400">Isi URL video legal atau placeholder untuk player.</p>

        <form action="{{ route('admin.episodes.store') }}" method="POST" class="mt-8">
            @include('admin.episodes._form')
        </form>
    </section>
@endsection
