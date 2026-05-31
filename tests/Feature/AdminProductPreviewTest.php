<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_product_preview(): void
    {
        $product = Product::query()->create([
            'name' => 'Produk Preview Guest',
            'slug' => 'produk-preview-guest',
            'normal_price' => 99000,
            'is_active' => false,
        ]);

        $response = $this->get(route('admin.products.preview', $product));

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_preview_for_inactive_product_without_file(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::query()->create([
            'name' => 'Produk Preview Admin',
            'slug' => 'produk-preview-admin',
            'normal_price' => 150000,
            'is_active' => false,
            'published_at' => null,
            'digital_file_path' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.preview', $product));

        $response->assertOk();
        $response->assertSee('Mode Preview Admin');
        $response->assertSee('Produk belum tersedia untuk checkout.');
        $response->assertSee('noindex,nofollow', false);
    }

    public function test_non_visible_product_still_not_accessible_on_public_detail(): void
    {
        Storage::fake('private');

        $product = Product::query()->create([
            'name' => 'Produk Tidak Visible',
            'slug' => 'produk-tidak-visible',
            'normal_price' => 110000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/999/missing.zip',
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertNotFound();
    }
}
