@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="glass-card rounded-[32px] p-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Back</a>
                    <p class="mt-4 text-sm uppercase tracking-[0.3em] text-slate-500">Notification Center</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Broadcast product, payment, and platform messages.</h1>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="glass-card rounded-[24px] px-5 py-4 text-sm text-slate-700">{{ session('status') }}</div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Send Notification</h2>
                <form method="POST" action="{{ route('admin.notifications.store') }}" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Title</label>
                        <input type="text" name="title" required class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Message</label>
                        <textarea name="message" rows="5" required class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900"></textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Audience</label>
                        <select name="audience" class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                            <option value="all">All users</option>
                            <option value="premium">Premium users</option>
                            <option value="free">Free users</option>
                            <option value="single">Single user</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Single user target</label>
                        <select name="user_id" class="w-full rounded-2xl border border-slate-300 bg-white/80 px-4 py-3 text-slate-900">
                            <option value="">Select user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} · {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Send Notification</button>
                </form>
            </div>

            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Sent Notifications</h2>
                <div class="mt-6 space-y-3">
                    @foreach ($notifications as $notification)
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900">{{ $notification->title }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $notification->message }}</p>
                            <p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-400">
                                {{ $notification->audience }} · {{ optional($notification->sent_at)->format('d M Y h:i A') }}
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection
