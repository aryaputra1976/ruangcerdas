@extends('layouts.admin')

@php
    $title = 'Tambah Testimonial';
    $subtitle = 'Tambahkan social proof baru dari pembeli.';
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Tambah Testimonial</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.testimonials.store') }}">
            @csrf
            @include('admin.testimonials._form')
        </form>
    </div>
</div>
@endsection
