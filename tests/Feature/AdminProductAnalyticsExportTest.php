<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductAnalyticsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_product_analytics_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$category, $product] = $this->seedProductWithOrder();

        $response = $this->actingAs($admin)->get(route('admin.analytics.products.export', [
            'from' => now()->toDateString(),
            'to' => now()->toDateString(),
            'product_id' => $product->id,
            'category_id' => $category->id,
            'status' => Order::STATUS_PAID,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Product Name', $content);
        $this->assertStringContainsString('Template Export', $content);
    }

    public function test_guest_cannot_access_product_analytics_export(): void
    {
        $response = $this->get(route('admin.analytics.products.export'));

        $response->assertRedirect('/login');
    }

    private function seedProductWithOrder(): array
    {
        $category = Category::query()->create([
            'name' => 'Template',
            'slug' => 'template',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Template Export',
            'slug' => 'template-export',
            'normal_price' => 150000,
            'is_active' => true,
            'published_at' => now(),
        ]);

        Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'INV-RC-ANL-001',
            'buyer_name' => 'Buyer Export',
            'buyer_email' => 'buyer-export@example.com',
            'buyer_whatsapp' => '081234567899',
            'price' => 150000,
            'status' => Order::STATUS_PAID,
            'payment_method' => 'manual',
            'download_count' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$category, $product];
    }
}
