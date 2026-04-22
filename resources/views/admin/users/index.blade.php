@extends('layouts.app')

@section('title', 'Fer6origami | Admin User')

@section('content')
    <section class="space-y-6">
        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin User</p>
            <h1 class="mt-2 font-display text-4xl font-bold text-white">Kelola user</h1>
        </div>

        <div class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
            <form action="{{ route('admin.users.index') }}" method="GET" class="mb-6 flex flex-col gap-3 sm:flex-row">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..." class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-500">
                <button type="submit" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">Cari</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase tracking-[0.3em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-t border-white/10">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </td>
                                <td class="px-4 py-4">{{ strtoupper($user->role) }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/5">Edit</a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-red-500/20 px-3 py-2 text-xs font-semibold text-red-200 transition hover:bg-red-500/10">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $users->links() }}</div>
        </div>
    </section>
@endsection
