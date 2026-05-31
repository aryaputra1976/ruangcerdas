<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomerContactListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_customer_contact_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.customers.index'));

        $response->assertOk();
        $response->assertSee('Daftar Kontak Customer');
    }

    public function test_guest_cannot_access_customer_contact_list(): void
    {
        $response = $this->get(route('admin.customers.index'));

        $response->assertRedirect('/login');
    }

    public function test_customer_list_can_render_when_orders_empty(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.customers.index'));

        $response->assertOk();
        $response->assertSee('Belum ada data customer dari order.');
    }

    public function test_customer_list_search_q_does_not_error(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::query()->create([
            'name' => 'Produk Customer',
            'slug' => 'produk-customer',
            'normal_price' => 100000,
            'is_active' => true,
            'published_at' => now(),
        ]);

        Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'INV-RC-CUST-001',
            'buyer_name' => 'Budi Customer',
            'buyer_email' => 'budi@example.com',
            'buyer_whatsapp' => '081234567890',
            'price' => 100000,
            'status' => Order::STATUS_PAID,
            'payment_method' => 'manual',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.customers.index', ['q' => 'Budi']));

        $response->assertOk();
        $response->assertSee('Budi Customer');
    }
}
