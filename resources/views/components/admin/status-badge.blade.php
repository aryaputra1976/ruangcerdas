@props([
    'status' => null,
])

@php
    $label = match ($status) {
        'pending' => 'Pending',
        'payment_uploaded' => 'Menunggu Verifikasi',
        'paid' => 'Paid',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        default => ucfirst((string) $status),
    };

    $class = match ($status) {
        'pending' => 'bg-warning-subtle text-warning',
        'payment_uploaded' => 'bg-primary-subtle text-primary',
        'paid' => 'bg-success-subtle text-success',
        'rejected' => 'bg-danger-subtle text-danger',
        'expired' => 'bg-secondary-subtle text-secondary',
        default => 'bg-secondary-subtle text-secondary',
    };
@endphp

<span class="badge {{ $class }} fw-semibold rounded-pill">
    {{ $label }}
</span>
