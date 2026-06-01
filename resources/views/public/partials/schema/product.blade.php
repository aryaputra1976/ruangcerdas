@php
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => $product->short_description ?: strip_tags((string) $product->description),
        'sku' => 'RC-' . $product->id,
        'category' => $product->category?->name,
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Ruang Cerdas',
        ],
        'url' => route('products.show', $product->slug),
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) ((int) $price),
            'priceCurrency' => 'IDR',
            'availability' => $canCheckout ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('products.show', $product->slug),
            'seller' => [
                '@type' => 'Organization',
                'name' => 'Ruang Cerdas',
            ],
        ],
    ];

    if (!empty($coverUrl)) {
        $productSchema['image'] = [$coverUrl];
    }
@endphp
<script type="application/ld+json">@json($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>

