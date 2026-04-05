@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('exams.index') }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Exam Preview</p>
            <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ $exam->title }}</h1>
            <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">{{ $exam->description }}</p>
            <div class="mt-6 flex flex-wrap gap-3">
                @if ($canStart)
                    <form method="POST" action="{{ route('exams.start', $exam) }}">
                        @csrf
                        <button class="btn-primary" type="submit">Begin Timed Exam</button>
                    </form>
                @elseif (! auth()->user()->hasActivePremium())
                    <a href="{{ route('subscription.show') }}" class="btn-primary">Unlock with GBP {{ $subscriptionPrice }}/month</a>
                @endif
                <a href="{{ route('exams.index') }}" class="btn-secondary">Back to Exam Library</a>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            @foreach ($exam->sections as $section)
                <article class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">{{ $section->name }}</h2>
                    <p class="mt-3 text-sm text-slate-600">{{ $section->questions->count() }} questions</p>
                    <p class="mt-2 text-sm text-slate-600">{{ round($section->time_limit_seconds / 60) }} minute section timer</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $section->questions->count() }} marks available in this section</p>
                </article>
            @endforeach
        </section>

        <section class="glass-card rounded-[28px] p-6">
            <h2 class="font-display text-2xl font-semibold text-slate-900">UCAT-Style Pattern</h2>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                This mock follows the UCAT-style structure with timed sections, section-by-section scoring, and detailed marks tracking so students can measure both pace and accuracy.
            </p>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ($exam->sections as $section)
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="font-semibold text-slate-900">{{ $section->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $section->questions->count() }} questions · {{ round($section->time_limit_seconds / 60) }} minutes · {{ $section->questions->count() }} marks</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
