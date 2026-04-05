@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Student Tracking</p>
            <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ $student->name }}</h1>
            <p class="mt-3 text-sm text-slate-600">{{ $student->email }}</p>
            <div class="mt-5">
                <a href="{{ route('admin.users.show', $student) }}" class="btn-secondary">Open Business Controls</a>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Exam attempts</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $report['attempt_count'] }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Login records</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ $report['logins']->count() }}</p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Weak sections</p><p class="mt-3 text-4xl font-semibold text-slate-950">{{ count($report['weak_sections']) }}</p></div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1fr_1fr]">
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Accuracy Trend</h2>
                <div class="mt-6 space-y-3">
                    @foreach ($report['accuracy_trend'] as $point)
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900">{{ $point['exam'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $point['accuracy'] }}% accuracy</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Recent Activity</h2>
                <div class="mt-6 space-y-3">
                    @foreach ($report['sessions'] as $session)
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900">{{ ucfirst($session->event) }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ optional($session->started_at)->format('d M Y h:i A') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
