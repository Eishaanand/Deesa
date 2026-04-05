@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Attempt Report</p>
            <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ $attempt->exam->title }}</h1>
            @if (($attempt->exam->sequence_number ?? 0) >= 3 && ! auth()->user()->hasActivePremium())
                <div class="mt-6 rounded-3xl bg-amber-50 px-5 py-4 text-sm text-amber-700">
                    You have completed the first three mocks. Continue with premium access for GBP {{ $subscriptionPrice }}/month.
                    <a href="{{ route('subscription.show') }}" class="ml-2 font-semibold underline">Upgrade now</a>
                </div>
            @endif
            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Score</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $report['summary']['total_score'] ?? 0 }}</p>
                    <p class="mt-2 text-sm text-slate-500">Out of {{ $report['summary']['total_questions'] ?? 0 }} marks</p>
                </div>
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Accuracy</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $report['summary']['accuracy_percentage'] ?? 0 }}%</p>
                </div>
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Correct</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $report['summary']['correct_answers'] ?? 0 }}</p>
                </div>
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Incorrect</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $report['summary']['incorrect_answers'] ?? 0 }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Section Breakdown</h2>
                <div class="mt-6 space-y-3">
                    @foreach (($report['summary']['section_breakdown'] ?? []) as $section)
                        <div class="glass-card-strong rounded-3xl p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $section['section'] }}</p>
                                    <p class="text-sm text-slate-500">{{ $section['score'] }}/{{ $section['total_questions'] }} marks · {{ $section['average_time_seconds'] }} sec average per question</p>
                                </div>
                                <p class="text-lg font-semibold text-slate-900">{{ $section['accuracy_percentage'] }}%</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Weak Topics</h2>
                    <div class="mt-5 space-y-3">
                        @foreach ($report['weak_topics'] as $topic)
                            <div class="rounded-3xl bg-slate-950 px-4 py-4 text-white">
                                <p class="font-semibold">{{ $topic['section'] }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ $topic['accuracy_percentage'] }}% accuracy</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">AI Suggestions</h2>
                    <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-600">
                        @foreach ($report['suggestions'] as $suggestion)
                            <li>{{ $suggestion }}</li>
                        @endforeach
                    </ul>
                    @if (! auth()->user()->hasActivePremium())
                        <div class="mt-6">
                            <a href="{{ route('subscription.show') }}" class="btn-secondary">Premium GBP {{ $subscriptionPrice }}/month</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="glass-card rounded-[28px] p-6">
            <h2 class="font-display text-2xl font-semibold text-slate-900">Question Review</h2>
            <div class="mt-6 space-y-8">
                @foreach ($report['review_sections'] as $section)
                    <div>
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="font-display text-xl font-semibold text-slate-900">{{ $section['section'] }}</h3>
                            <span class="text-sm text-slate-500">{{ count($section['questions']) }} questions</span>
                        </div>

                        <div class="space-y-4">
                            @foreach ($section['questions'] as $index => $question)
                                <article class="glass-card-strong rounded-[24px] p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">
                                            Question {{ $index + 1 }}
                                        </p>
                                        <span class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] {{ $question['is_correct'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $question['is_correct'] ? 'Correct' : 'Incorrect' }}
                                        </span>
                                    </div>

                                    @if ($question['passage'])
                                        <div class="mt-4 rounded-3xl bg-white/70 p-5 text-sm leading-7 text-slate-600">
                                            {{ $question['passage'] }}
                                        </div>
                                    @endif

                                    <h4 class="mt-5 text-lg font-semibold leading-8 text-slate-900">{{ $question['stem'] }}</h4>

                                    <div class="mt-5 space-y-3">
                                        @foreach ($question['options'] as $option)
                                            @php
                                                $isSelected = $question['selected_answer'] === $option;
                                                $isCorrectOption = $question['correct_answer'] === $option;
                                                $optionClass = $isCorrectOption
                                                    ? 'border-emerald-300 bg-emerald-50 text-emerald-800'
                                                    : ($isSelected && ! $question['is_correct']
                                                        ? 'border-red-300 bg-red-50 text-red-800'
                                                        : 'border-slate-200 bg-white/70 text-slate-700');
                                            @endphp
                                            <div class="rounded-3xl border px-4 py-4 text-sm {{ $optionClass }}">
                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                    <span>{{ $option }}</span>
                                                    <div class="flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.2em]">
                                                        @if ($isCorrectOption)
                                                            <span>Correct answer</span>
                                                        @endif
                                                        @if ($isSelected)
                                                            <span>Your answer</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-5 grid gap-4 lg:grid-cols-[0.9fr_1.1fr]">
                                        <div class="rounded-3xl bg-slate-100 px-4 py-4 text-sm text-slate-600">
                                            <p class="font-semibold text-slate-900">Answer Summary</p>
                                            <p class="mt-2">Your answer: {{ $question['selected_answer'] ?? 'Not answered' }}</p>
                                            <p class="mt-1">Correct answer: {{ $question['correct_answer'] }}</p>
                                            <p class="mt-1">Time spent: {{ $question['time_spent_seconds'] }} seconds</p>
                                        </div>
                                        <div class="rounded-3xl bg-sky-50 px-4 py-4 text-sm leading-7 text-slate-700">
                                            <p class="font-semibold text-slate-900">Explanation</p>
                                            <p class="mt-2">{{ $question['explanation'] }}</p>
                                            <p class="mt-3 font-semibold text-slate-900">How to avoid the same mistake</p>
                                            <p class="mt-2">{{ $question['mistake_advice'] }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
