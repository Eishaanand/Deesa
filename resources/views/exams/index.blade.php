@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Explore Exams</p>
            <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Practice full UCAT-style timed exams.</h1>
            @if (! auth()->user()->hasActivePremium())
                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">
                    The first three mocks unlock one by one. Additional mocks require premium access at GBP 30 per month.
                </p>
            @endif
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($examCards as $card)
                @php($exam = $card['exam'])
                <article class="glass-card rounded-[28px] p-6">
                    <p class="text-sm uppercase tracking-[0.25em] text-slate-500">{{ $exam->status }}</p>
                    <h2 class="mt-3 font-display text-2xl font-semibold text-slate-900">{{ $exam->title }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $exam->description }}</p>
                    <div class="mt-5 flex items-center justify-between text-sm text-slate-500">
                        <span>Mock {{ $exam->sequence_number }} · {{ $exam->sections->count() }} sections</span>
                        <span>{{ round($exam->total_duration_seconds / 60) }} minutes</span>
                    </div>
                    @if ($card['subscription_required'])
                        <div class="mt-4 rounded-3xl bg-amber-50 px-4 py-4 text-sm text-amber-700">
                            Premium required after the first three mocks. Upgrade for GBP 30/month.
                        </div>
                    @elseif (! $card['unlocked'])
                        <div class="mt-4 rounded-3xl bg-slate-100 px-4 py-4 text-sm text-slate-600">
                            Complete the previous mock to unlock this exam automatically.
                        </div>
                    @endif
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('exams.show', $exam) }}" class="btn-secondary">View</a>
                        @if ($card['can_start'])
                            <form method="POST" action="{{ route('exams.start', $exam) }}">
                                @csrf
                                <button class="btn-primary" type="submit">Start Exam</button>
                            </form>
                        @elseif ($card['subscription_required'])
                            <a href="{{ route('subscription.show') }}" class="btn-primary">Go Premium</a>
                        @else
                            <button class="btn-primary opacity-60" type="button" disabled>Locked</button>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endsection
