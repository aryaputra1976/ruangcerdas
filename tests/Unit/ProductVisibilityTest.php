<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_is_visible_to_public_when_private_file_exists(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/demo.pdf', 'demo');

        $product = Product::query()->create([
            'name' => 'Demo Product',
            'slug' => 'demo-product',
            'normal_price' => 10000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/demo.pdf',
        ]);

        $this->assertTrue($product->isVisibleToPublic());
    }

    public function test_product_is_not_visible_to_public_when_private_file_missing(): void
    {
        Storage::fake('private');

        $product = Product::query()->create([
            'name' => 'Hidden Product',
            'slug' => 'hidden-product',
            'normal_price' => 10000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/missing.pdf',
        ]);

        $this->assertFalse($product->isVisibleToPublic());
    }
}

