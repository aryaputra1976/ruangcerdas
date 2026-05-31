<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_can_render_with_notification_summary(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_public_home_is_not_affected_by_admin_notification_summary(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}
