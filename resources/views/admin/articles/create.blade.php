@extends('layouts.admin')

@php
    $title = 'Tambah Artikel';
    $subtitle = 'Buat artikel edukasi baru.';
@endphp

@section('content')
<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Form Artikel</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.articles._form')
        </form>
    </div>
</div>
@endsection
