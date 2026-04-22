@extends('layouts.app')

@section('title', 'Fer6origami | Edit Banner')

@section('content')
    <section class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
        <h1 class="font-display text-4xl font-bold text-white">Edit banner</h1>
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @method('PUT')
            @include('admin.banners._form')
        </form>
    </section>
@endsection
