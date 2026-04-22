@if (session('success') || session('error'))
    <div class="mb-6 space-y-3">
        @if (session('success'))
            <div class="brand-frame flex items-start gap-4 rounded-[28px] border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-200">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-emerald-400/20 bg-emerald-400/10 font-display text-sm font-bold text-emerald-100">
                    OK
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-200/70">Success</p>
                    <p class="mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="brand-frame flex items-start gap-4 rounded-[28px] border border-red-500/30 bg-red-500/10 px-5 py-4 text-sm text-red-200">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-red-400/20 bg-red-400/10 font-display text-sm font-bold text-red-100">
                    !
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-red-200/70">Notice</p>
                    <p class="mt-1">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>
@endif
