@props(['title', 'message'])

<div class="brand-frame overflow-hidden rounded-[32px] border border-dashed border-white/15 bg-white/5 px-6 py-16 text-center shadow-card">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[22px] border border-white/10 bg-gradient-to-br from-ember-500/20 to-tide-400/20 font-display text-2xl font-bold text-white">
        F6
    </div>
    <p class="mt-5 text-xs uppercase tracking-[0.35em] text-slate-500">Nothing Here Yet</p>
    <h3 class="mt-3 font-display text-3xl font-bold text-white">{{ $title }}</h3>
    <p class="mx-auto mt-3 max-w-2xl text-sm leading-7 text-slate-400">{{ $message }}</p>
</div>
