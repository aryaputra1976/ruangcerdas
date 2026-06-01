<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoTechnicalTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_contains_sitemap_and_is_plain_text(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Sitemap: https://ruangcerdas.id/sitemap.xml', false);
    }

    public function test_sitemap_is_accessible_and_does_not_include_sensitive_paths(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertDontSee('/admin', false);
        $response->assertDontSee('/checkout', false);
        $response->assertDontSee('/order/', false);
    }

    public function test_public_product_pages_are_indexable_and_have_schema(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/seo-schema.pdf', 'schema');

        $product = Product::query()->create([
            'name' => 'Produk Schema',
            'slug' => 'produk-schema',
            'normal_price' => 75000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/seo-schema.pdf',
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertDontSee('noindex,nofollow', false);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertDontSee('noindex,nofollow', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_sensitive_pages_are_noindex(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/seo-noindex.pdf', 'schema');

        $product = Product::query()->create([
            'name' => 'Produk Checkout',
            'slug' => 'produk-checkout',
            'normal_price' => 75000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/seo-noindex.pdf',
        ]);

        $this->get(route('checkout.create', $product->slug))
            ->assertOk()
            ->assertSee('noindex,nofollow', false);
    }
}

