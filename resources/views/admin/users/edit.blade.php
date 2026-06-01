@extends('layouts.admin')

@php
    $title = 'Edit User';
    $subtitle = $user->name;
@endphp

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h5 class="card-title mb-1">Form Edit User</h5>
            <p class="text-muted mb-0 fs-13">Perbarui data user sesuai kebutuhan.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3">
            Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users._form', ['user' => $user])
        </form>
    </div>
</div>

@endsection

