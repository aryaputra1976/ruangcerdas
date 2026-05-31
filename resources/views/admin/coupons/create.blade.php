@extends('layouts.admin')

@php
    $title = 'Tambah Kupon';
    $subtitle = 'Buat kode diskon baru.';
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Tambah Kupon</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            @include('admin.coupons._form')
        </form>
    </div>
</div>
@endsection
