@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Exam Management</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Create, edit, and expand AI-enabled UCAT exams.</h1>
                </div>
                <a href="{{ route('admin.exams.create') }}" class="btn-primary">New Exam</a>
            </div>
        </section>

        @if (session('status'))
            <section class="glass-card rounded-[24px] px-5 py-4 text-sm text-slate-700">
                {{ session('status') }}
            </section>
        @endif

        <div class="space-y-4">
            @foreach ($exams as $exam)
                <article class="glass-card rounded-[28px] p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="font-display text-2xl font-semibold text-slate-900">{{ $exam->title }}</h2>
                            <p class="mt-2 text-sm text-slate-500">{{ $exam->sections_count }} sections · {{ $exam->status }}</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.exams.show', $exam) }}" class="btn-secondary">Open</a>
                            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Delete this exam and all user attempt data for it?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary">Delete</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endsection
