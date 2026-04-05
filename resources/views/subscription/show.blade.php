@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ url()->previous() }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Premium Subscription</p>
            @if (auth()->user()->hasActivePremium())
                <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Premium access is active.</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">
                    You already have access to all premium mock exams, analytics, and AI-driven features.
                </p>
            @else
                <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Unlock premium UCAT mocks for GBP {{ $price }}/month.</h1>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">
                    Premium access unlocks additional AI-generated mock exams, ongoing analytics, and continued progression after the first three mock tests.
                </p>
            @endif

            @if (session('status'))
                <div class="mt-6 rounded-3xl bg-emerald-50 px-4 py-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-8 flex flex-wrap gap-4">
                @if (! auth()->user()->hasActivePremium())
                    <form method="POST" action="{{ route('subscription.activate') }}">
                        @csrf
                        <button type="submit" class="btn-primary">Activate Premium</button>
                    </form>
                @endif
                <a href="{{ route('exams.index') }}" class="btn-secondary">Back to Exams</a>
            </div>
        </section>
    </div>
@endsection
