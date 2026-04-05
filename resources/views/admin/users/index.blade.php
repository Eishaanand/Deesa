@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="glass-card rounded-[32px] p-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Back</a>
                    <p class="mt-4 text-sm uppercase tracking-[0.3em] text-slate-500">User Management</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Users, subscriptions, and account operations.</h1>
                </div>
                <div class="glass-card rounded-full px-5 py-3 text-sm text-slate-700">
                    Premium plan: GBP {{ $price }}/month
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-6 flex flex-wrap gap-3">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search users by name or email" class="min-w-[18rem] rounded-full border border-slate-300 bg-white/80 px-4 py-3 text-sm text-slate-900">
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </section>

        @if (session('status'))
            <div class="glass-card rounded-[24px] px-5 py-4 text-sm text-slate-700">{{ session('status') }}</div>
        @endif

        <div class="space-y-4">
            @foreach ($users as $user)
                <article class="glass-card rounded-[28px] p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="font-display text-2xl font-semibold text-slate-900">{{ $user->name }}</h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $user->email }}</p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ ucfirst($user->role->value) }} · {{ ucfirst($user->subscription_status) }} · {{ $user->exam_attempts_count }} attempts
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.students.show', $user) }}" class="btn-secondary">Analytics</a>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-primary">Manage</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div>
            {{ $users->links() }}
        </div>
    </div>
@endsection
