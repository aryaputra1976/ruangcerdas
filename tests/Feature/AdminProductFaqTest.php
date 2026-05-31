<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductFaq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductFaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_faq(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->makePublicVisibleProduct('produk-faq-1');

        $response = $this->actingAs($admin)->post(route('admin.products.faqs.store', $product), [
            'question' => 'Apakah produk ini bisa langsung dipakai?',
            'answer' => 'Ya, file siap pakai setelah download.',
            'is_active' => '1',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.products.faqs.index', $product));
        $this->assertDatabaseHas('product_faqs', [
            'product_id' => $product->id,
            'question' => 'Apakah produk ini bisa langsung dipakai?',
            'is_active' => 1,
        ]);
    }

    public function test_active_faq_is_visible_on_public_product_detail(): void
    {
        $product = $this->makePublicVisibleProduct('produk-faq-2');
        $product->faqs()->create([
            'question' => 'FAQ Aktif Produk',
            'answer' => 'Ini jawaban aktif.',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Pertanyaan tentang produk ini');
        $response->assertSee('FAQ Aktif Produk');
    }

    public function test_inactive_faq_is_not_visible_on_public_product_detail(): void
    {
        $product = $this->makePublicVisibleProduct('produk-faq-3');
        $product->faqs()->create([
            'question' => 'FAQ Nonaktif Produk',
            'answer' => 'Ini jawaban nonaktif.',
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertDontSee('FAQ Nonaktif Produk');
    }

    public function test_faq_cannot_be_updated_through_different_product_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $productA = $this->makePublicVisibleProduct('produk-faq-a');
        $productB = $this->makePublicVisibleProduct('produk-faq-b');

        $faq = ProductFaq::query()->create([
            'product_id' => $productA->id,
            'question' => 'FAQ A',
            'answer' => 'Jawaban A',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.products.faqs.update', [$productB, $faq]), [
            'question' => 'Diubah',
            'answer' => 'Tidak boleh',
            'is_active' => '1',
            'sort_order' => 0,
        ]);

        $response->assertNotFound();
    }

    private function makePublicVisibleProduct(string $slug): Product
    {
        Storage::fake('private');
        Storage::disk('private')->put("products/1/{$slug}.zip", 'dummy file');

        return Product::query()->create([
            'name' => 'Produk FAQ ' . $slug,
            'slug' => $slug,
            'normal_price' => 120000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => "products/1/{$slug}.zip",
        ]);
    }
}
