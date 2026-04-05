@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Manage User</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ $user->name }}</h1>
                    <p class="mt-3 text-sm text-slate-600">{{ $user->email }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('admin.users.premium', $user) }}">
                        @csrf
                        <button type="submit" class="{{ $user->subscription_status === 'premium' && $user->premium_until?->isFuture() ? 'btn-secondary' : 'btn-primary' }}">
                            {{ $user->subscription_status === 'premium' && $user->premium_until?->isFuture() ? 'Deactivate Premium' : 'Activate Premium' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary">Delete User</button>
                    </form>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="glass-card rounded-[24px] px-5 py-4 text-sm text-slate-700">{{ session('status') }}</div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Account Controls</h2>
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Avatar URL</label>
                        <input type="url" name="avatar_url" value="{{ old('avatar_url', $user->avatar_url) }}" class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Role</label>
                            <select name="role" class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                                <option value="student" @selected($user->role->value === 'student')>Student</option>
                                <option value="admin" @selected($user->role->value === 'admin')>Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Subscription</label>
                            <select name="subscription_status" class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                                <option value="free" @selected($user->subscription_status === 'free')>Free</option>
                                <option value="premium" @selected($user->subscription_status === 'premium')>Premium</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Business Summary</h2>
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <p>Status: {{ ucfirst($user->subscription_status) }}</p>
                        <p>Premium until: {{ optional($user->premium_until)->format('d M Y') ?? 'Not active' }}</p>
                        <p>Attempts: {{ $user->examAttempts->count() }}</p>
                        <p>Notifications: {{ $user->notifications->count() }}</p>
                    </div>
                </div>

                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Recent Notifications</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($user->notifications->sortByDesc('sent_at')->take(5) as $notification)
                            <div class="glass-card-strong rounded-3xl p-4">
                                <p class="font-semibold text-slate-900">{{ $notification->title }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $notification->message }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No notifications sent to this user yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
