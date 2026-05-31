<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_terms_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.terms'));

        $response->assertOk();
        $response->assertSeeText('Syarat');
        $response->assertSeeText('Ketentuan');
    }

    public function test_privacy_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.privacy'));

        $response->assertOk();
        $response->assertSee('Kebijakan Privasi');
    }
}
