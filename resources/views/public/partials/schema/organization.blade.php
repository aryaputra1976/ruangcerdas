@php
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Ruang Cerdas',
        'url' => route('home'),
        'logo' => asset('hando/assets/images/rc/rc_mark.svg'),
    ];

    if (!empty($supportNumber ?? null)) {
        $organizationSchema['contactPoint'] = [[
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'telephone' => '+' . ltrim((string) $supportNumber, '+'),
            'areaServed' => 'ID',
            'availableLanguage' => ['id'],
        ]];
    }
@endphp
<script type="application/ld+json">@json($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
