<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecureDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_order_with_valid_token_can_download_private_file(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/file.zip', 'test-content');

        $product = Product::query()->create([
            'name' => 'Template ZIP',
            'slug' => 'template-zip',
            'normal_price' => 59000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/file.zip',
            'download_filename' => 'template.zip',
        ]);

        $order = Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'RC-TEST-0001',
            'buyer_name' => 'Budi',
            'buyer_email' => 'budi@example.com',
            'buyer_whatsapp' => '081234567890',
            'price' => 59000,
            'status' => Order::STATUS_PAID,
            'payment_method' => 'manual',
            'download_token' => 'valid-token',
            'download_expires_at' => now()->addDay(),
            'download_count' => 0,
        ]);

        $response = $this->get(route('orders.download', [
            'invoice' => $order->invoice_number,
            'token' => 'valid-token',
        ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertDatabaseHas('download_logs', [
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_invalid_download_token_returns_403(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('products/file.zip', 'test-content');

        $product = Product::query()->create([
            'name' => 'Template ZIP',
            'slug' => 'template-zip-2',
            'normal_price' => 59000,
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'digital_file_path' => 'products/file.zip',
        ]);

        $order = Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'RC-TEST-0002',
            'buyer_name' => 'Sinta',
            'buyer_email' => 'sinta@example.com',
            'buyer_whatsapp' => '081234567891',
            'price' => 59000,
            'status' => Order::STATUS_PAID,
            'payment_method' => 'manual',
            'download_token' => 'valid-token',
            'download_expires_at' => now()->addDay(),
            'download_count' => 0,
        ]);

        $response = $this->get(route('orders.download', [
            'invoice' => $order->invoice_number,
            'token' => 'wrong-token',
        ]));

        $response->assertForbidden();
    }
}

