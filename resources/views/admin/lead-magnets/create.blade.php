@extends('layouts.admin')
@php($title = 'Tambah Lead Magnet')
@php($subtitle = 'Buat panduan gratis baru.')
@section('content')
<div class="card"><div class="card-header"><h5 class="card-title mb-0">Form Lead Magnet</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.lead-magnets.store') }}" enctype="multipart/form-data">@csrf @include('admin.lead-magnets._form')</form></div></div>
@endsection
