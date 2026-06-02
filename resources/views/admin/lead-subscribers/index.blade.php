@extends('layouts.admin')
@php($title = 'Lead Subscribers')
@php($subtitle = 'Data calon pembeli dari panduan gratis.')
@section('content')
<div class="card"><div class="card-header"><h5 class="card-title mb-0">Daftar Subscriber</h5></div><div class="card-body">
@if($subscribers->count())
<div class="table-responsive table-card"><table class="table table-hover align-middle mb-0"><thead class="table-light text-muted"><tr><th>Email</th><th>Nama</th><th>WhatsApp</th><th>Lead Magnet</th><th>Downloaded At</th></tr></thead><tbody>@foreach($subscribers as $s)<tr><td>{{ $s->email }}</td><td>{{ $s->name ?: '-' }}</td><td>{{ $s->whatsapp ?: '-' }}</td><td>{{ $s->leadMagnet->title ?? '-' }}</td><td>{{ $s->downloaded_at?->format('d M Y H:i') ?: '-' }}</td></tr>@endforeach</tbody></table></div><div class="mt-3">{{ $subscribers->links() }}</div>
@else
<p class="text-muted mb-0">Belum ada subscriber.</p>
@endif
</div></div>
@endsection
