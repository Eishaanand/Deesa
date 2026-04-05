<aside class="glass-card rounded-[28px] p-5 lg:sticky lg:top-6">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">Deesa AI</p>
        <h2 class="mt-2 font-display text-2xl font-semibold text-slate-900">UCAT Command</h2>
    </div>

    <nav class="space-y-2 text-sm">
        <a href="{{ route('dashboard') }}" class="block rounded-2xl px-4 py-3 text-slate-700 transition hover:bg-white/70">About Platform</a>
        <a href="{{ route('dashboard') }}#ucat" class="block rounded-2xl px-4 py-3 text-slate-700 transition hover:bg-white/70">About UCAT</a>
        <a href="{{ route('exams.index') }}" class="block rounded-2xl px-4 py-3 text-slate-700 transition hover:bg-white/70">Explore Exams</a>
        <a href="{{ route('dashboard') }}#performance" class="block rounded-2xl px-4 py-3 text-slate-700 transition hover:bg-white/70">My Performance</a>
        @if (! auth()->user()?->hasActivePremium())
            <a href="{{ route('subscription.show') }}" class="block rounded-2xl px-4 py-3 text-slate-700 transition hover:bg-white/70">Premium GBP 30/month</a>
        @endif
        @if (auth()->user()?->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl px-4 py-3 text-slate-700 transition hover:bg-white/70">Admin Business</a>
        @endif
    </nav>
</aside>
