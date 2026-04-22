@extends('layouts.app')

@section('title', 'Fer6origami | Admin Banner')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin Banner</p>
                <h1 class="mt-2 font-display text-4xl font-bold text-white">Kelola banner</h1>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">
                Tambah Banner
            </a>
        </div>

        <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($banners as $banner)
                    <div class="overflow-hidden rounded-[28px] border border-white/10 bg-white/5">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="aspect-[16/9] w-full object-cover">
                        <div class="space-y-3 p-5">
                            <h2 class="font-display text-2xl font-bold text-white">{{ $banner->title }}</h2>
                            <p class="text-sm leading-6 text-slate-400">{{ $banner->subtitle }}</p>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/5">Edit</a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border border-red-500/20 px-3 py-2 text-xs font-semibold text-red-200 transition hover:bg-red-500/10">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $banners->links() }}</div>
        </div>
    </section>
@endsection
