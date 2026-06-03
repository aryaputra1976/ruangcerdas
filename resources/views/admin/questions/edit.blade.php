@extends('layouts.admin')

@php
    $title = 'Edit Soal';
    $subtitle = $question->section . ' • ' . \Illuminate\Support\Str::limit(strip_tags($question->question_text), 60);
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Form Edit Soal</h5>
            <p class="text-muted mb-0 fs-13">Perbarui bank soal beserta opsi dan pembahasan.</p>
        </div>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">Kembali</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.questions.update', $question) }}">
            @csrf
            @method('PUT')
            @include('admin.questions._form', ['question' => $question])
        </form>
    </div>
</div>
@endsection
