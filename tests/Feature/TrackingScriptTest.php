<?php

namespace Tests\Feature;

use App\Models\LandingSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingScriptTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_scripts_are_rendered_when_ids_are_configured(): void
    {
        LandingSetting::query()->create([
            'meta_pixel_id' => '1234567890',
            'google_analytics_id' => 'G-TEST1234',
            'google_tag_manager_id' => 'GTM-TEST123',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('connect.facebook.net/en_US/fbevents.js', false);
        $response->assertSee('googletagmanager.com/gtm.js?id=', false);
        $response->assertSee('googletagmanager.com/ns.html?id=', false);
        $response->assertSee('window.rcTrack = function', false);
    }

    public function test_tracking_scripts_are_not_rendered_when_ids_are_empty(): void
    {
        LandingSetting::query()->create([
            'meta_pixel_id' => null,
            'google_analytics_id' => null,
            'google_tag_manager_id' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('connect.facebook.net/en_US/fbevents.js', false);
        $response->assertDontSee('googletagmanager.com/gtm.js?id=', false);
        $response->assertDontSee('googletagmanager.com/ns.html?id=', false);
        $response->assertSee('window.rcTrack = function', false);
    }
}

