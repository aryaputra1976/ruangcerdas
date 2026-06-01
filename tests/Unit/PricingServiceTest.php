<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_buyer_quota_counts_pending_and_payment_uploaded_orders(): void
    {
        $product = Product::query()->create([
            'name' => 'Produk Kuota',
            'slug' => 'produk-kuota',
            'normal_price' => 100000,
            'first_buyer_price' => 50000,
            'first_buyer_quota' => 2,
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'RC-QUOTA-0001',
            'buyer_name' => 'A',
            'buyer_email' => 'a@example.com',
            'buyer_whatsapp' => '081',
            'price' => 50000,
            'status' => Order::STATUS_PENDING,
        ]);

        Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'RC-QUOTA-0002',
            'buyer_name' => 'B',
            'buyer_email' => 'b@example.com',
            'buyer_whatsapp' => '082',
            'price' => 50000,
            'status' => Order::STATUS_PAYMENT_UPLOADED,
        ]);

        $resolved = app(PricingService::class)->resolve($product->fresh());

        $this->assertFalse($resolved['is_first_buyer']);
        $this->assertSame(100000, $resolved['price']);
    }
}

