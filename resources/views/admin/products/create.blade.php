@extends('layouts.admin')

@php
    $title = 'Tambah Produk';
    $subtitle = 'Tambahkan produk digital baru untuk dijual di Ruang Cerdas.';
@endphp

@section('content')

<form method="POST"
      action="{{ route('admin.products.store') }}"
      enctype="multipart/form-data">
    @csrf

    @include('admin.products._form')
</form>

@endsection