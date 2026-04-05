@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.exams.show', $exam) }}" class="btn-secondary">Back</a>
    </div>
    @include('admin.exams.partials.form', [
        'title' => 'Edit Exam',
        'action' => route('admin.exams.update', $exam),
        'method' => 'PUT',
        'exam' => $exam,
    ])
@endsection
