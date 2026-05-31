<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_note_to_order(): void
    {
        [$admin, $order] = $this->adminAndOrder();

        $response = $this->actingAs($admin)->post(route('admin.orders.notes.store', $order), [
            'note' => 'Bukti transfer terlihat valid, menunggu approval Kabid.',
            'is_pinned' => '1',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertDatabaseHas('order_notes', [
            'order_id' => $order->id,
            'user_id' => $admin->id,
            'is_pinned' => 1,
        ]);
    }

    public function test_admin_can_delete_note_from_order(): void
    {
        [$admin, $order] = $this->adminAndOrder();
        $note = $order->notes()->create([
            'user_id' => $admin->id,
            'note' => 'Follow up via WhatsApp.',
            'is_pinned' => false,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.orders.notes.destroy', [$order, $note]));

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertDatabaseMissing('order_notes', [
            'id' => $note->id,
        ]);
    }

    public function test_guest_cannot_add_note_to_order(): void
    {
        [, $order] = $this->adminAndOrder();

        $response = $this->post(route('admin.orders.notes.store', $order), [
            'note' => 'Tidak boleh masuk.',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_internal_note_is_not_shown_on_public_order_tracking(): void
    {
        [$admin, $order] = $this->adminAndOrder();
        $order->notes()->create([
            'user_id' => $admin->id,
            'note' => 'CATATAN INTERNAL RAHASIA',
            'is_pinned' => true,
        ]);

        $response = $this->post(route('public.order-tracking.show'), [
            'invoice_number' => $order->invoice_number,
            'contact' => $order->buyer_email,
        ]);

        $response->assertOk();
        $response->assertDontSee('CATATAN INTERNAL RAHASIA');
    }

    private function adminAndOrder(): array
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::query()->create([
            'name' => 'Template Proposal',
            'slug' => 'template-proposal',
            'normal_price' => 99000,
            'is_active' => true,
            'published_at' => now(),
        ]);

        $order = Order::query()->create([
            'product_id' => $product->id,
            'invoice_number' => 'INV-RC-NOTE-001',
            'buyer_name' => 'Ruang Cerdas User',
            'buyer_email' => 'buyer@example.com',
            'buyer_whatsapp' => '08123456789',
            'price' => 99000,
            'status' => Order::STATUS_PAYMENT_UPLOADED,
            'payment_method' => 'manual',
        ]);

        return [$admin, $order];
    }
}
