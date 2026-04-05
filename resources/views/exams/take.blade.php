@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('exams.index') }}" class="btn-secondary">Back</a>
        </div>

        <livewire:exam-runner :attempt="$attempt" />
    </div>
@endsection
