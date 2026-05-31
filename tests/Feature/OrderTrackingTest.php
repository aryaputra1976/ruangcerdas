<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_form_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.order-tracking.index'));

        $response->assertOk();
        $response->assertSee('Cek Status Order');
    }

    public function test_valid_invoice_and_email_can_show_order_tracking_result(): void
    {
        $product = Product::query()->create([
            'name' => 'Template Surat',
            'slug' => 'template-surat',
            'normal_price' => 99000,
            'is_active' => true,
            'published_at' => now(),
        ]);

        $order = Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'INV-RC-TEST-001',
            'buyer_name' => 'Budi',
            'buyer_email' => 'budi@example.com',
            'buyer_whatsapp' => '0812-3456-7890',
            'price' => 99000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'manual',
        ]);

        $response = $this->post(route('public.order-tracking.show'), [
            'invoice_number' => $order->invoice_number,
            'contact' => 'BUDI@example.com',
        ]);

        $response->assertOk();
        $response->assertSee($order->invoice_number);
        $response->assertSee('Order dibuat, silakan lakukan pembayaran dan upload bukti bayar.');
    }

    public function test_valid_invoice_and_whatsapp_can_show_order_tracking_result(): void
    {
        $product = Product::query()->create([
            'name' => 'Ebook Produktif',
            'slug' => 'ebook-produktif',
            'normal_price' => 49000,
            'is_active' => true,
            'published_at' => now(),
        ]);

        $order = Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'INV-RC-TEST-002',
            'buyer_name' => 'Sinta',
            'buyer_email' => 'sinta@example.com',
            'buyer_whatsapp' => '6281234567890',
            'price' => 49000,
            'status' => Order::STATUS_PAYMENT_UPLOADED,
            'payment_method' => 'manual',
        ]);

        $response = $this->post(route('public.order-tracking.show'), [
            'invoice_number' => $order->invoice_number,
            'contact' => '+62 812 3456 7890',
        ]);

        $response->assertOk();
        $response->assertSee('Bukti bayar sudah diterima, menunggu verifikasi admin.');
    }

    public function test_invalid_contact_will_not_show_order_result(): void
    {
        $product = Product::query()->create([
            'name' => 'Kit AI',
            'slug' => 'kit-ai',
            'normal_price' => 129000,
            'is_active' => true,
            'published_at' => now(),
        ]);

        $order = Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'INV-RC-TEST-003',
            'buyer_name' => 'Andi',
            'buyer_email' => 'andi@example.com',
            'buyer_whatsapp' => '08111111111',
            'price' => 129000,
            'status' => Order::STATUS_PAID,
            'payment_method' => 'manual',
        ]);

        $response = $this->from(route('public.order-tracking.index'))
            ->post(route('public.order-tracking.show'), [
                'invoice_number' => $order->invoice_number,
                'contact' => 'salah@example.com',
            ]);

        $response->assertRedirect(route('public.order-tracking.index'));
        $response->assertSessionHasErrors('tracking');
    }
}
