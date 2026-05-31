<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFaqPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.faq'));

        $response->assertOk();
        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Masih butuh bantuan?');
    }

    public function test_sitemap_contains_faq_url(): void
    {
        $response = $this->get(route('public.sitemap'));

        $response->assertOk();
        $response->assertSee(route('public.faq'), false);
    }
}
