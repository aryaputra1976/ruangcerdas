<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchemaMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_renders_product_and_breadcrumb_schema(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/schema-product.pdf', 'schema');

        $product = Product::query()->create([
            'name' => 'Schema Product',
            'slug' => 'schema-product',
            'short_description' => 'Produk untuk pengujian schema',
            'normal_price' => 99000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/schema-product.pdf',
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('"@type":"Product"', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"priceCurrency":"IDR"', false);
        $response->assertSee('"seller":{"@type":"Organization","name":"Ruang Cerdas"}', false);
    }

    public function test_product_detail_renders_faq_schema_only_when_active_faq_exists(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/schema-faq.pdf', 'schema');

        $product = Product::query()->create([
            'name' => 'Schema FAQ Product',
            'slug' => 'schema-faq-product',
            'normal_price' => 120000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/schema-faq.pdf',
        ]);

        $product->faqs()->create([
            'question' => 'Apakah ini FAQ aktif?',
            'answer' => 'Ya, ini tampil di schema.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('"@type":"FAQPage"', false);
    }

    public function test_schema_markup_does_not_expose_sensitive_fields(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/schema-safe.pdf', 'schema');

        $product = Product::query()->create([
            'name' => 'Schema Safe Product',
            'slug' => 'schema-safe-product',
            'normal_price' => 65000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/schema-safe.pdf',
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertDontSee('digital_file_path', false);
        $response->assertDontSee('download_token', false);
        $response->assertDontSee('invoice_number', false);
    }
}

