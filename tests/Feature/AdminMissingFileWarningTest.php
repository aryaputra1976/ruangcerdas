<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMissingFileWarningTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_product_without_file_is_shown_as_missing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Product::query()->create([
            'name' => 'Produk Tanpa File',
            'slug' => 'produk-tanpa-file',
            'normal_price' => 10000,
            'is_active' => true,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.index', ['file_status' => 'missing']));

        $response->assertOk();
        $response->assertSee('File belum ada');
    }

    public function test_active_product_with_valid_private_file_is_shown_as_ready(): void
    {
        Storage::fake('private');

        $admin = User::factory()->create(['role' => 'admin']);

        $path = 'products/1/file-siap.zip';
        Storage::disk('private')->put($path, 'dummy-content');

        Product::query()->create([
            'name' => 'Produk Dengan File',
            'slug' => 'produk-dengan-file',
            'normal_price' => 20000,
            'is_active' => true,
            'published_at' => now(),
            'digital_file_path' => $path,
            'download_filename' => 'file-siap.zip',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.index', ['file_status' => 'ready']));

        $response->assertOk();
        $response->assertSee('File siap');
    }
}
