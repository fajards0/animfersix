@extends('layouts.app')

@section('title', 'Fer6origami | Edit User')

@section('content')
    <section class="glass-panel rounded-[32px] border border-white/10 p-6 shadow-glow">
        <h1 class="font-display text-4xl font-bold text-white">Edit user</h1>
        <p class="mt-2 text-sm text-slate-400">Perbarui identitas, role, dan password user bila diperlukan.</p>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="mt-8 space-y-5">
            @csrf
            @method('PUT')
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                </div>
            </div>
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Role</label>
                    <select name="role" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                        <option value="user" @selected(old('role', $user->role) === 'user')>User</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-300">Password Baru</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white">
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">Batal</a>
            </div>
        </form>
    </section>
@endsection
