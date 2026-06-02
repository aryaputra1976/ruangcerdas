@extends('layouts.admin')
@php($title = 'Edit Lead Magnet')
@php($subtitle = $leadMagnet->title)
@section('content')
<div class="card"><div class="card-header"><h5 class="card-title mb-0">Form Edit Lead Magnet</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.lead-magnets.update', $leadMagnet) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.lead-magnets._form', ['leadMagnet' => $leadMagnet])</form></div></div>
@endsection
