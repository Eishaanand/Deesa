@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="glass-card rounded-[32px] p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Admin</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Deesa UCAT AI Control center</h1>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">Users</a>
                    <a href="{{ route('admin.notifications.index') }}" class="btn-secondary">Notifications</a>
                    <a href="{{ route('admin.exams.index') }}" class="btn-secondary">Exams</a>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-4">
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Total users</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $metrics['total_users'] }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Total students</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $metrics['total_students'] }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Premium users</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $metrics['premium_users'] }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Active users</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $metrics['active_users'] }}</p></div>
        </section>

        <section class="grid gap-6 xl:grid-cols-4">
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">MRR</p><p class="mt-3 text-4xl font-semibold text-slate-950">GBP {{ $metrics['monthly_recurring_revenue'] }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">ARR</p><p class="mt-3 text-4xl font-semibold text-slate-950">GBP {{ $metrics['projected_annual_revenue'] }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Avg score</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $metrics['average_score'] }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Avg accuracy</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $metrics['average_accuracy'] }}%</p></div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Business Alerts</h2>
                <div class="mt-6 space-y-3">
                    @forelse ($alerts as $alert)
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900">{{ $alert['title'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $alert['message'] }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No alerts right now.</p>
                    @endforelse
                </div>
            </div>

            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Sales Dashboard</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="text-sm text-slate-500">Conversion</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $sales['conversion_rate'] }}%</p>
                    </div>
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="text-sm text-slate-500">Free users</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $sales['free_users'] }}</p>
                    </div>
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="text-sm text-slate-500">Paying users</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $sales['premium_users'] }}</p>
                    </div>
                </div>
                <div class="mt-6 space-y-3">
                    @foreach ($sales['recent_premium_users'] as $user)
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900">{{ $user['name'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $user['email'] }} · active until {{ $user['premium_until'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1fr_1fr]">
            <div class="glass-card rounded-[28px] p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Live Monitoring</h2>
                    <a href="{{ route('admin.exams.index') }}" class="btn-secondary">Manage Exams</a>
                </div>
                <div class="mt-6 space-y-3">
                    @forelse ($live as $item)
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900">{{ $item['student'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $item['exam'] }} · {{ $item['section'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $item['time_elapsed_minutes'] }} minutes elapsed</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No students are currently taking an exam.</p>
                    @endforelse
                </div>
            </div>

            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Performance Distribution</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($distribution as $range => $count)
                        <div>
                            <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                <span>{{ $range }}</span>
                                <span>{{ $count }}</span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-200">
                                <div class="h-3 rounded-full bg-slate-950" style="width: {{ min($count * 12, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="glass-card rounded-[28px] p-6">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Recent Notifications</h2>
                <a href="{{ route('admin.notifications.index') }}" class="btn-secondary">Open Notification Center</a>
            </div>
            <div class="mt-6 space-y-3">
                @forelse ($notifications as $notification)
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="font-semibold text-slate-900">{{ $notification['title'] }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $notification['recipient'] }} · {{ $notification['audience'] }} · {{ $notification['sent_at'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No notifications have been sent yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
