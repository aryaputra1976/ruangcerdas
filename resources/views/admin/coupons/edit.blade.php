@extends('layouts.admin')

@php
    $title = 'Edit Kupon';
    $subtitle = $coupon->code;
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Edit Kupon</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
            @csrf
            @method('PUT')
            @include('admin.coupons._form', ['coupon' => $coupon])
        </form>
    </div>
</div>
@endsection
