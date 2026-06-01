@php
    $trackingSetting = \App\Models\LandingSetting::query()->first();
    $googleTagManagerId = trim((string) ($trackingSetting?->google_tag_manager_id ?? ''));
@endphp

@if ($googleTagManagerId !== '')
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ e($googleTagManagerId) }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
@endif

