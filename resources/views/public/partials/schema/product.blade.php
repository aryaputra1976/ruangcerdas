@php
    $productUrl = route('products.show', $product->slug);

    $schemaDescription = trim(strip_tags((string) ($product->short_description ?: $product->description ?: 'Produk digital Ruang Cerdas.')));
    $schemaImage = !empty($coverUrl)
        ? $coverUrl
        : asset('hando/assets/images/rc/rc_ico.png');

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => $schemaDescription,
        'image' => [$schemaImage],
        'url' => $productUrl,
        'sku' => 'RC-' . $product->id,
        'category' => $product->category?->name,
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Ruang Cerdas',
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => $productUrl,
            'priceCurrency' => 'IDR',
            'price' => (string) ((int) $price),
            'availability' => $canCheckout ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => [
                '@type' => 'Organization',
                'name' => 'Ruang Cerdas',
                'url' => url('/'),
            ],
            'shippingDetails' => [
                '@type' => 'OfferShippingDetails',
                'shippingRate' => [
                    '@type' => 'MonetaryAmount',
                    'value' => 0,
                    'currency' => 'IDR',
                ],
                'shippingDestination' => [
                    '@type' => 'DefinedRegion',
                    'addressCountry' => 'ID',
                ],
                'deliveryTime' => [
                    '@type' => 'ShippingDeliveryTime',
                    'handlingTime' => [
                        '@type' => 'QuantitativeValue',
                        'minValue' => 0,
                        'maxValue' => 1,
                        'unitCode' => 'DAY',
                    ],
                    'transitTime' => [
                        '@type' => 'QuantitativeValue',
                        'minValue' => 0,
                        'maxValue' => 1,
                        'unitCode' => 'DAY',
                    ],
                ],
            ],
            'hasMerchantReturnPolicy' => [
                '@type' => 'MerchantReturnPolicy',
                'applicableCountry' => 'ID',
                'returnPolicyCategory' => 'https://schema.org/MerchantReturnNotPermitted',
                'merchantReturnDays' => 0,
                'returnFees' => 'https://schema.org/FreeReturn',
                'returnMethod' => 'https://schema.org/ReturnByMail',
            ],
        ],
    ];

    $visibleReviews = ($product->relationLoaded('reviews') ? $product->reviews : collect())
        ->filter(fn ($review) => (bool) $review->is_visible)
        ->values();
    $reviewCount = $visibleReviews->count();

    if ($reviewCount > 0) {
        $ratingValue = round((float) $visibleReviews->avg('rating'), 1);

        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $ratingValue,
            'reviewCount' => (string) $reviewCount,
        ];
    }

    $schemaReviews = $visibleReviews
        ->take(3)
        ->map(function ($review) {
            return [
                '@type' => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name' => $review->author_name,
                ],
                'datePublished' => ($review->reviewed_at ?? $review->created_at ?? now())->format('Y-m-d'),
                'reviewBody' => $review->body,
                'name' => $review->title ?: 'Ulasan Produk Ruang Cerdas',
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => (string) $review->rating,
                    'bestRating' => '5',
                    'worstRating' => '1',
                ],
            ];
        })
        ->values()
        ->all();

    if (!empty($schemaReviews)) {
        $schema['review'] = $schemaReviews;
    }
@endphp
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
