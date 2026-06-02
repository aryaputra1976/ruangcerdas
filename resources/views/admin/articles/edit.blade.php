@extends('layouts.admin')

@php
    $title = 'Edit Artikel';
    $subtitle = $article->title;
@endphp

@section('content')
<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Form Edit Artikel</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.articles._form', ['article' => $article])
        </form>
    </div>
</div>
@endsection
