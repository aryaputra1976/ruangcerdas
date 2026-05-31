@extends('layouts.admin')

@php
    $title = 'Edit Testimonial';
    $subtitle = $testimonial->name;
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Edit Testimonial</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}">
            @csrf
            @method('PUT')
            @include('admin.testimonials._form', ['testimonial' => $testimonial])
        </form>
    </div>
</div>
@endsection
