<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductPublishValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_publish_product_when_inactive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::query()->create([
            'name' => 'Produk Draft',
            'slug' => 'produk-draft',
            'normal_price' => 50000,
            'is_active' => true,
            'published_at' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'slug' => $product->slug,
            'normal_price' => $product->normal_price,
            'is_active' => '0',
            'is_published' => '1',
        ]);

        $response->assertSessionHasErrors(['is_published']);
    }

    public function test_cannot_publish_without_digital_file_and_can_publish_with_new_upload(): void
    {
        Storage::fake('private');

        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::query()->create([
            'name' => 'Produk Tanpa File',
            'slug' => 'produk-tanpa-file-publish',
            'normal_price' => 75000,
            'is_active' => true,
            'published_at' => null,
            'digital_file_path' => null,
        ]);

        $invalid = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'slug' => $product->slug,
            'normal_price' => $product->normal_price,
            'is_active' => '1',
            'is_published' => '1',
        ]);

        $invalid->assertSessionHasErrors(['digital_file']);

        $valid = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'slug' => $product->slug,
            'normal_price' => $product->normal_price,
            'is_active' => '1',
            'is_published' => '1',
            'digital_file' => UploadedFile::fake()->create('produk.zip', 64, 'application/zip'),
        ]);

        $valid->assertSessionHasNoErrors();
        $valid->assertRedirect(route('admin.products.edit', $product));

        $product->refresh();

        $this->assertNotNull($product->published_at);
        $this->assertNotNull($product->digital_file_path);
        Storage::disk('private')->assertExists($product->digital_file_path);
    }
}
