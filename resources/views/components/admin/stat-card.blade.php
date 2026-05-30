@props([
    'title' => '',
    'value' => 0,
    'description' => '',
    'icon' => 'bar-chart-2',
    'color' => 'primary',
    'url' => null,
])

@php
    $colorClass = match ($color) {
        'success' => 'success',
        'warning' => 'warning',
        'danger' => 'danger',
        'secondary' => 'secondary',
        'info' => 'info',
        default => 'primary',
    };
@endphp

<div class="col-md-6 col-lg-4 col-xl">
    @if ($url)
        <a href="{{ $url }}" class="text-decoration-none">
    @endif

    <div class="card">
        <div class="card-body">
            <div class="widget-first">
                <div class="d-flex align-items-center mb-2">
                    <div class="p-2 border border-{{ $colorClass }} border-opacity-10 bg-{{ $colorClass }}-subtle rounded-2 me-2">
                        <div class="bg-{{ $colorClass }} rounded-circle widget-size text-center">
                            <i data-feather="{{ $icon }}" class="text-white" style="width: 18px; height: 18px;"></i>
                        </div>
                    </div>

                    <p class="mb-0 text-dark fs-15">
                        {{ $title }}
                    </p>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fs-22 text-dark me-3">
                        {{ $value }}
                    </h3>

                    <div class="text-end">
                        <p class="text-muted fs-13 mb-0">
                            {{ $description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($url)
        </a>
    @endif
</div>