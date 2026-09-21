<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_renders_gasket_landing(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Gasket')
            ->assertSee('banking details changed')
            ->assertSee('Pricing', false)
            ->assertSee('Documentation', false);
    }

    public function test_landing_links_login_register_and_sections(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false)
            ->assertSee('id="features"', false)
            ->assertSee('id="how"', false)
            ->assertSee('id="pricing"', false)
            ->assertSee('id="docs"', false)
            ->assertSee('$29', false);
    }
}
