@extends('layouts.guest')

@section('content')
    <div class="relative">
        <div class="absolute inset-x-0 top-0 -z-10 h-[36rem] bg-[radial-gradient(circle_at_top,rgba(143,211,255,0.32),transparent_34%)]"></div>

        <header class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="glass-card flex items-center justify-between rounded-full px-5 py-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Deesa UCAT AI</p>
                </div>
                <div class="flex gap-3">
                    <a href="/login" class="btn-secondary">Login</a>
                    <a href="/register" class="btn-primary">Sign Up · GBP 30/month Premium</a>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 pb-20 pt-8 sm:px-6 lg:px-8">
            <section class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
                <div class="glass-card rounded-[36px] p-8 lg:p-12">
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-500">AI-Powered UCAT Preparation</p>
                    <h1 class="mt-5 max-w-3xl font-display text-5xl font-semibold leading-tight text-slate-950 lg:text-7xl">
                        Train like test day with adaptive mocks, live tracking, and AI-generated UCAT practice.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Build timing control, diagnose weak sections, and let the platform surface targeted practice using real attempt analytics.
                    </p>
                    <div class="mt-6 rounded-3xl bg-slate-950 px-5 py-4 text-sm text-white">
                        First three mocks unlock sequentially. Premium continuation is GBP 30 per month.
                    </div>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="/register" class="btn-primary">Start Preparing</a>
                        <a href="/login" class="btn-secondary">Student Login</a>
                    </div>
                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="text-3xl font-semibold text-slate-900">{{ $examCount }}</p>
                            <p class="mt-1 text-sm text-slate-600">Mock exams ready</p>
                        </div>
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="text-3xl font-semibold text-slate-900">5</p>
                            <p class="mt-1 text-sm text-slate-600">UCAT sections covered</p>
                        </div>
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="text-3xl font-semibold text-slate-900">AI</p>
                            <p class="mt-1 text-sm text-slate-600">Dynamic question generation</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="glass-card rounded-[32px] p-6">
                        <p class="text-sm font-semibold text-slate-900">Features</p>
                        <ul class="mt-4 space-y-4 text-sm text-slate-600">
                            <li>Real-time exam runner with autosave and timed sections.</li>
                            <li>Performance analytics for accuracy, speed, and weak-topic detection.</li>
                            <li>Admin console for student tracking, exam authoring, and AI generation.</li>
                        </ul>
                    </div>
                    <div class="glass-card rounded-[32px] p-6">
                        <p class="text-sm font-semibold text-slate-900">About UCAT</p>
                        <p class="mt-4 text-sm leading-7 text-slate-600">
                            The UCAT evaluates reasoning speed, decision accuracy, numerical confidence, pattern recognition, and judgement under pressure.
                        </p>
                    </div>
                </div>
            </section>

            <section class="mt-10 grid gap-6 lg:grid-cols-3">
                <article class="glass-card rounded-[28px] p-6">
                    <p class="text-sm font-semibold text-slate-900">AI analytics</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">Spot weak sections, compare attempt trends, and receive practice suggestions after each mock.</p>
                </article>
                <article class="glass-card rounded-[28px] p-6">
                    <p class="text-sm font-semibold text-slate-900">Real-time exams</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">Sequential sections with fixed timers simulate actual UCAT pace without section skipping.</p>
                </article>
                <article class="glass-card rounded-[28px] p-6">
                    <p class="text-sm font-semibold text-slate-900">Performance tracking</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">Measure total score, section accuracy, question speed, and long-term improvement over time.</p>
                </article>
            </section>
        </main>
    </div>
@endsection
