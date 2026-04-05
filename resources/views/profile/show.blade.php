@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ url()->previous() }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">My Profile</p>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <h1 class="font-display text-4xl font-semibold text-slate-950">{{ $user->name }}</h1>
                        @if ($user->hasActivePremium())
                            <span class="rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-emerald-700">
                                Premium
                            </span>
                        @endif
                    </div>
                    <p class="mt-3 text-sm text-slate-600">{{ $user->email }}</p>
                </div>
                <div class="h-24 w-24 overflow-hidden rounded-full border border-white/60 bg-white/80">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-3xl font-semibold text-slate-500">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="glass-card rounded-[28px] p-6">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Edit Details</h2>
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Dashboard</a>
                </div>

                @if (session('status'))
                    <div class="mt-5 rounded-3xl bg-emerald-50 px-4 py-4 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-3xl bg-red-50 px-4 py-4 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div>
                        <label for="avatar_url" class="mb-2 block text-sm font-medium text-slate-700">Photo URL</label>
                        <input id="avatar_url" name="avatar_url" type="url" value="{{ old('avatar_url', $user->avatar_url) }}" placeholder="https://example.com/photo.jpg" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-slate-900 outline-none transition focus:border-sky-300">
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="btn-primary">Save Profile</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-secondary">Logout</button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Subscription</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        Status: <span class="font-semibold">{{ $user->hasActivePremium() ? 'Premium' : 'Free' }}</span>
                    </p>
                    <p class="mt-2 text-sm leading-7 text-slate-600">
                        Price: GBP {{ $price }}/month
                    </p>
                    <p class="mt-2 text-sm leading-7 text-slate-600">
                        Active until: {{ optional($user->premium_until)->format('d M Y') ?? 'No active plan' }}
                    </p>
                    @if (! $user->hasActivePremium())
                        <div class="mt-6">
                            <a href="{{ route('subscription.show') }}" class="btn-secondary">Upgrade to Premium</a>
                        </div>
                    @endif
                </div>

                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Account Snapshot</h2>
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <p>Role: {{ ucfirst($user->role->value) }}</p>
                        <p>Last seen: {{ optional($user->last_seen_at)->format('d M Y h:i A') ?? 'No activity yet' }}</p>
                        @if (! $user->hasActivePremium())
                            <p>Premium continuation after mock 3 is GBP {{ $price }}/month.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
