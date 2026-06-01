<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_contains_conversion_sections_and_cta(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Lihat Produk');
        $response->assertSee('Cocok Untuk Siapa');
        $response->assertSee('Cara Beli');
        $response->assertSee('Kenapa aman membeli di Ruang Cerdas?');
        $response->assertSee('FAQ');
        $response->assertDontSee('noindex,nofollow', false);
    }

    public function test_product_pages_render_mobile_sticky_cta_and_track_hooks(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/landing-conversion.pdf', 'ok');

        $product = Product::query()->create([
            'name' => 'Landing Conversion Product',
            'slug' => 'landing-conversion-product',
            'normal_price' => 89000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/landing-conversion.pdf',
        ]);

        $index = $this->get(route('products.index'));
        $index->assertOk();
        $index->assertSee('fixed inset-x-0 bottom-0', false);

        $show = $this->get(route('products.show', $product->slug));
        $show->assertOk();
        $show->assertSee('fixed inset-x-0 bottom-0', false);
        $show->assertSee("window.rcTrack && window.rcTrack('ViewContent'", false);
    }
}

