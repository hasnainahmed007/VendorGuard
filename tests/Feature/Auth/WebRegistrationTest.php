<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WebRegistrationTest extends TestCase
{
    // DatabaseMigrations (not RefreshDatabase): tenant provisioning runs DDL
    // which cannot live inside a transaction, same as production.
    use DatabaseMigrations;

    private function cleanup(Tenant ...$tenants): void
    {
        foreach ($tenants as $tenant) {
            Tenant::find($tenant->getKey())?->delete();
        }
    }

    public function test_web_signup_provisions_workspace_and_owner(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Acme Inc',
            'email' => 'owner@acme.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/app/incidents');

        $tenant = Tenant::where('email', 'owner@acme.test')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'email' => 'owner@acme.test',
            'role' => 'owner',
            'tenant_id' => $tenant->getKey(),
        ]);

        $this->assertAuthenticated();

        $this->get('/app/incidents')->assertOk();

        $this->cleanup($tenant);
    }

    public function test_web_signup_rejects_duplicate_email(): void
    {
        $payload = [
            'name' => 'Acme Inc',
            'email' => 'dup@test.example',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post(route('register.store'), $payload)->assertRedirect('/app/incidents');

        $tenant = Tenant::where('email', 'dup@test.example')->firstOrFail();

        $this->post(route('logout'));

        $this->post(route('register.store'), $payload)
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        // No second workspace was provisioned.
        $this->assertSame(1, Tenant::where('email', 'dup@test.example')->count());

        $this->cleanup($tenant);
    }
}
