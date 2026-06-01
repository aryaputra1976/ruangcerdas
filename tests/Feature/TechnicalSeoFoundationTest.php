<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechnicalSeoFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_is_accessible_and_contains_production_sitemap_line(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *', false);
        $response->assertSee('Sitemap: https://ruangcerdas.id/sitemap.xml', false);
    }

    public function test_sitemap_is_accessible_and_excludes_sensitive_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee(route('home'), false);
        $response->assertSee(route('products.index'), false);
        $response->assertSee(route('public.faq'), false);
        $response->assertSee(route('public.terms'), false);
        $response->assertSee(route('public.privacy'), false);
        $response->assertDontSee('/admin', false);
        $response->assertDontSee('/order/', false);
        $response->assertDontSee('/checkout/', false);
        $response->assertDontSee('/download/', false);
    }

    public function test_public_pages_do_not_use_noindex(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('noindex,nofollow', false);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertDontSee('noindex,nofollow', false);

        $this->get(route('public.faq'))
            ->assertOk()
            ->assertDontSee('noindex,nofollow', false);
    }

    public function test_sensitive_pages_use_noindex_nofollow(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/seo-check.pdf', 'seo-check');

        $product = Product::query()->create([
            'name' => 'SEO Check Product',
            'slug' => 'seo-check-product',
            'normal_price' => 10000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/seo-check.pdf',
        ]);

        $this->get(route('public.order-tracking.index'))
            ->assertOk()
            ->assertSee('noindex,nofollow', false);

        $this->get(route('checkout.create', $product->slug))
            ->assertOk()
            ->assertSee('noindex,nofollow', false);
    }
}

