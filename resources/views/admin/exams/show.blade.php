@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.exams.index') }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr] xl:items-start">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Exam Detail</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">{{ $exam->title }}</h1>
                    <p class="mt-2 text-sm text-slate-500">
                        Source mode: {{ $exam->is_ai_supported ? 'Gemini' : 'Local' }}
                    </p>
                    <div class="mt-5 glass-card-strong rounded-[28px] p-5">
                        <div class="grid gap-3 md:grid-cols-3 md:items-end">
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Question Source</label>
                                <form method="POST" action="{{ route('admin.exams.toggle-source', $exam) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm font-semibold text-slate-700">
                                        {{ $exam->is_ai_supported ? 'Gemini' : 'Local' }}
                                    </button>
                                </form>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Generate</label>
                                <form method="POST" action="{{ route('admin.exams.regenerate', $exam) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary w-full">Generate</button>
                                </form>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Delete</label>
                                <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Delete this exam and all user attempt data for it?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm font-semibold text-slate-700">Delete Exam</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:admin-question-generator :exam="$exam" />
            </div>
        </section>

        @if (session('status'))
            <section class="glass-card rounded-[24px] px-5 py-4 text-sm text-slate-700">
                {{ session('status') }}
            </section>
        @endif

        <section class="space-y-4">
            @foreach ($exam->sections as $section)
                @php
                    $availableGeminiCount = \App\Models\Question::query()
                        ->where('source', 'gemini')
                        ->whereHas('section', fn ($query) => $query->where('type', $section->type)->where('exam_id', '!=', $exam->id))
                        ->count();

                    $availableLocalCount = \App\Models\Question::query()
                        ->whereIn('source', ['local_bank', 'manual', 'seed', 'fallback'])
                        ->whereHas('section', fn ($query) => $query->where('type', $section->type)->where('exam_id', '!=', $exam->id))
                        ->count();
                @endphp
                <article class="glass-card rounded-[28px] p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="font-display text-2xl font-semibold text-slate-900">{{ $section->name }}</h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $section->questions->count() }} questions · {{ round($section->time_limit_seconds / 60) }} mins</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Available Gemini questions: {{ $availableGeminiCount }}
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Available local cached questions: {{ $availableLocalCount }}
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Questions already in this exam: {{ $section->questions->count() }}
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    </div>
@endsection
