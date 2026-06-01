@php
    $defaultSiteName = 'Ruang Cerdas';
    $defaultTitle = 'Ruang Cerdas - Produk Digital Siap Pakai';
    $defaultDescription = 'Marketplace produk digital seperti template, ebook, file ZIP, dan aplikasi siap pakai.';
    $defaultType = 'website';
    $defaultCanonical = url()->current();

    $seoTitle = trim($__env->yieldContent('title', $defaultTitle)) ?: $defaultTitle;
    $seoDescription = trim($__env->yieldContent('meta_description', $defaultDescription)) ?: $defaultDescription;
    $seoCanonical = trim($__env->yieldContent('canonical', $defaultCanonical)) ?: $defaultCanonical;
    $seoOgUrl = trim($__env->yieldContent('og_url', $seoCanonical)) ?: $seoCanonical;
    $seoType = trim($__env->yieldContent('og_type', $defaultType)) ?: $defaultType;
    $seoSiteName = trim($__env->yieldContent('site_name', $defaultSiteName)) ?: $defaultSiteName;
    $seoRobots = trim($__env->yieldContent('robots', 'index,follow')) ?: 'index,follow';
    $seoKeywords = trim($__env->yieldContent('meta_keywords', ''));
    $seoOgImage = trim($__env->yieldContent('og_image', ''));
    $twitterCard = $seoOgImage !== '' ? 'summary_large_image' : 'summary';
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
@if ($seoKeywords !== '')
    <meta name="keywords" content="{{ $seoKeywords }}">
@endif
<link rel="canonical" href="{{ $seoCanonical }}">

<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:url" content="{{ $seoOgUrl }}">
<meta property="og:site_name" content="{{ $seoSiteName }}">
@if ($seoOgImage !== '')
    <meta property="og:image" content="{{ $seoOgImage }}">
@endif

<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@if ($seoOgImage !== '')
    <meta name="twitter:image" content="{{ $seoOgImage }}">
@endif
