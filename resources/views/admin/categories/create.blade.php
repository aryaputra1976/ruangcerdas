@extends('layouts.admin')

@php
    $title = 'Tambah Kategori';
    $subtitle = 'Tambahkan kategori baru untuk produk digital Ruang Cerdas.';
@endphp

@section('content')

<form method="POST" action="{{ route('admin.categories.store') }}">
    @csrf

    @include('admin.categories._form')
</form>

@endsection