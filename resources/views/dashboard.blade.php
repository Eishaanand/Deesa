@extends('layouts.app')

@section('content')
    <div class="grid gap-6 lg:grid-cols-[290px_1fr]">
        <x-sidebar />

        <div class="space-y-6">
            <section class="glass-card rounded-[32px] p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Student Dashboard</p>
                <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Train with AI-guided UCAT analytics.</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">
                    The platform analyzes speed, accuracy, and section consistency to reveal where your preparation loses marks.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @if (! auth()->user()->hasActivePremium())
                        <a href="{{ route('subscription.show') }}" class="btn-primary">Premium for GBP {{ $subscriptionPrice }}/month</a>
                    @endif
                    @if ($overview['active_attempt'])
                        <a href="{{ route('exams.take', $overview['active_attempt']) }}" class="btn-secondary">
                            {{ $overview['active_attempt']->status === 'paused' ? 'Resume Paused Exam' : 'Continue Active Exam' }}
                        </a>
                    @endif
                </div>
            </section>

            <section id="ucat" class="grid gap-6 lg:grid-cols-2">
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">About The Platform</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        Each exam attempt is tracked question by question. You get weak-section detection, performance trend snapshots, and suggested next practice areas.
                    </p>
                </div>
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">About UCAT</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        UCAT preparation requires both correctness and pace. This dashboard emphasizes section timing and error patterns, not only total score.
                    </p>
                </div>
            </section>

            <section id="performance" class="grid gap-6 xl:grid-cols-3">
                <div class="glass-card rounded-[28px] p-6">
                    <p class="text-sm text-slate-500">Latest score</p>
                    <p class="mt-3 text-4xl font-semibold text-slate-950">{{ number_format($overview['latest_marks']['score'], 0) }}</p>
                    <p class="mt-2 text-sm text-slate-500">{{ $overview['latest_marks']['out_of'] }} total marks in latest mock</p>
                </div>
                <div class="glass-card rounded-[28px] p-6">
                    <p class="text-sm text-slate-500">Average accuracy</p>
                    <p class="mt-3 text-4xl font-semibold text-slate-950">{{ number_format($overview['average_accuracy'], 1) }}%</p>
                </div>
                <div class="glass-card rounded-[28px] p-6">
                    <p class="text-sm text-slate-500">Attempts</p>
                    <p class="mt-3 text-4xl font-semibold text-slate-950">{{ $overview['submitted_count'] }}</p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <div class="glass-card rounded-[28px] p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-2xl font-semibold text-slate-900">Recent Attempts</h2>
                        <a href="{{ route('exams.index') }}" class="btn-secondary">Explore Exams</a>
                    </div>
                    <div class="mt-6 space-y-3">
                        @forelse ($overview['attempts'] as $attempt)
                            <div class="glass-card-strong rounded-3xl p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $attempt->exam->title }}</p>
                                        <p class="text-sm text-slate-500">{{ optional($attempt->submitted_at)->format('d M Y, h:i A') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-slate-900">{{ number_format((float) $attempt->accuracy_percentage, 1) }}%</p>
                                        <a href="{{ route('results.show', $attempt) }}" class="text-sm text-slate-600">View report</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No attempts yet. Start your first mock exam.</p>
                        @endforelse
                    </div>
                </div>

                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Weak Sections</h2>
                    <div class="mt-6 space-y-3">
                        @forelse ($overview['weak_sections'] as $section)
                            <div class="rounded-3xl bg-slate-950 px-4 py-4 text-white">
                                <p class="font-semibold">{{ $section['section'] }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ number_format($section['accuracy_percentage'], 1) }}% accuracy</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Weak-section insights will appear after your first completed exam.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">My Performance</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Track score and accuracy across recent mocks to see where you are improving and where marks are slipping.
                    </p>
                    <div class="mt-6 space-y-4">
                        @forelse ($overview['performance_graph']['points'] as $point)
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                    <span>{{ $point['label'] }}</span>
                                    <span>{{ number_format($point['score'], 0) }} marks · {{ number_format($point['accuracy'], 1) }}%</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-200">
                                    <div class="h-3 rounded-full bg-slate-950" style="width: {{ min(($point['score'] / max($overview['performance_graph']['score_max'], 1)) * 100, 100) }}%"></div>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-200">
                                    <div class="h-2 rounded-full bg-[#9adf79]" style="width: {{ min($point['accuracy'], 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Performance graphs will appear after your first submitted mock.</p>
                        @endforelse
                    </div>
                </div>

                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Marks Tracking</h2>
                    <div class="mt-6 space-y-3">
                        @forelse ($overview['section_marks'] as $section)
                            <div class="glass-card-strong rounded-3xl p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $section['section'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $section['score'] }}/{{ $section['out_of'] }} marks · {{ $section['average_time_seconds'] }} sec avg</p>
                                    </div>
                                    <p class="text-lg font-semibold text-slate-900">{{ number_format($section['accuracy_percentage'], 1) }}%</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Section marks will appear after your first completed mock.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
