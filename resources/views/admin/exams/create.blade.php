@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.exams.index') }}" class="btn-secondary">Back</a>
    </div>
    @include('admin.exams.partials.form', [
        'title' => 'Create Exam',
        'action' => route('admin.exams.store'),
        'method' => 'POST',
        'exam' => null,
    ])
@endsection
