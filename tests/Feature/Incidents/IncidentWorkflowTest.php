<?php

namespace Tests\Feature\Incidents;

use App\Models\AuditTrail;
use App\Models\Incident;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\VendorChangeDetectionService;
use App\Support\BankDetailHasher;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IncidentWorkflowTest extends TestCase
{
    // DatabaseMigrations (not RefreshDatabase): tenant provisioning runs DDL
    // which cannot live inside a transaction, same as production.
    use DatabaseMigrations;

    /**
     * @return array{tenant: Tenant, user: User}
     */
    private function makeTenantOwner(string $prefix, string $role = 'owner'): array
    {
        $tenant = Tenant::create([
            'company' => "{$prefix} Inc",
            'email' => "{$prefix}@test.example",
        ]);

        $user = User::create([
            'name' => "{$prefix} Owner",
            'email' => "{$prefix}@test.example",
            'role' => $role,
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->getKey(),
        ]);

        return ['tenant' => $tenant, 'user' => $user];
    }

    private function cleanup(Tenant ...$tenants): void
    {
        foreach ($tenants as $tenant) {
            Tenant::find($tenant->getKey())?->delete();
        }
    }

    public function test_manual_change_opens_incident_and_holds_vendor(): void
    {
        $owner = $this->makeTenantOwner('holdco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Payee Corp',
            'current_bank_last4' => '1111',
        ]);

        $response = $this->actingAs($owner['user'])->post(
            route('tenant.vendors.changes.store', ['vendor' => $vendor->getKey()]),
            [
                'field_changed' => 'bank_account',
                'old_value_hash' => 'last4:1111',
                'new_value_hash' => 'last4:2222',
                'source' => 'manual',
            ]
        );

        $incident = Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->firstOrFail();

        $response->assertRedirect(route('tenant.incidents.show', ['incident' => $incident->getKey()]));

        $this->assertSame('open', $incident->status);
        $this->assertSame('high', $incident->severity);
        $this->assertTrue($vendor->refresh()->payment_hold);

        $this->assertDatabaseHas('audit_trail', [
            'tenant_id' => $owner['tenant']->getKey(),
            'action' => 'incident.opened',
        ]);

        $this->cleanup($owner['tenant']);
    }

    public function test_duplicate_delivery_with_same_ref_opens_one_incident(): void
    {
        $owner = $this->makeTenantOwner('idemco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Repeat Ltd',
        ]);

        $payload = [
            'field_changed' => 'routing_number',
            'new_value_hash' => 'abc123',
            'source' => 'accounting',
            'raw_source_ref' => 'qb-webhook-1',
        ];

        $url = route('tenant.vendors.changes.store', ['vendor' => $vendor->getKey()]);

        $first = $this->actingAs($owner['user'])->post($url, $payload);
        $second = $this->actingAs($owner['user'])->post($url, $payload);

        $first->assertRedirect($second->headers->get('Location'));
        $this->assertSame(1, Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->count());

        $this->cleanup($owner['tenant']);
    }

    public function test_bank_detail_check_only_fires_on_real_divergence(): void
    {
        $owner = $this->makeTenantOwner('watchco');

        tenancy()->initialize($owner['tenant']);

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Steady Inc',
            'current_bank_last4' => '3333',
            'current_routing_hash' => BankDetailHasher::hash('021000021'),
        ]);

        $detection = app(VendorChangeDetectionService::class);

        // Same values: no incident.
        $this->assertNull($detection->checkBankDetails(
            $vendor, '3333', '021000021', 'accounting', 'poll-1'
        ));

        // Empty input: no incident.
        $this->assertNull($detection->checkBankDetails($vendor, null, null, 'accounting', 'poll-2'));

        // Routing changed: incident opens.
        $incident = $detection->checkBankDetails(
            $vendor, '3333', '011401142', 'accounting', 'poll-3'
        );

        $this->assertNotNull($incident);
        $this->assertSame('routing_number', $incident->changeLog->field_changed);
        $this->assertSame('high', $incident->severity);

        $this->cleanup($owner['tenant']);
    }

    public function test_verify_transition_releases_hold_and_adopts_hashes(): void
    {
        $owner = $this->makeTenantOwner('verifyco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Legit Change Co',
            'current_bank_last4' => '4444',
        ]);

        $this->actingAs($owner['user'])->post(
            route('tenant.vendors.changes.store', ['vendor' => $vendor->getKey()]),
            [
                'field_changed' => 'bank_account',
                'old_value_hash' => 'last4:4444',
                'new_value_hash' => 'last4:5555',
            ]
        );

        $incident = Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->firstOrFail();

        $this->actingAs($owner['user'])->post(
            route('tenant.incidents.transition', ['incident' => $incident->getKey()]),
            ['action' => 'verified', 'resolution_note' => 'Called +15550001111, confirmed.']
        )->assertRedirect(route('tenant.incidents.show', ['incident' => $incident->getKey()]));

        $vendor->refresh();

        $this->assertSame('verified', $incident->refresh()->status);
        $this->assertFalse($vendor->payment_hold);
        $this->assertSame('5555', $vendor->current_bank_last4);
        $this->assertNotNull($incident->resolved_at);

        $this->assertDatabaseHas('audit_trail', [
            'incident_id' => $incident->getKey(),
            'action' => 'incident.verified',
            'user_id' => $owner['user']->getKey(),
        ]);

        // A resolved incident cannot be actioned again.
        $this->actingAs($owner['user'])->post(
            route('tenant.incidents.transition', ['incident' => $incident->getKey()]),
            ['action' => 'blocked', 'resolution_note' => 'Too late.']
        )->assertRedirect()->assertSessionHasErrors('action');

        $this->cleanup($owner['tenant']);
    }

    public function test_blocked_transition_keeps_hold_and_requires_note(): void
    {
        $owner = $this->makeTenantOwner('blockco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Fraud Attempt LLC',
        ]);

        $this->actingAs($owner['user'])->post(
            route('tenant.vendors.changes.store', ['vendor' => $vendor->getKey()]),
            ['field_changed' => 'bank_account', 'new_value_hash' => 'last4:6666']
        );

        $incident = Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->firstOrFail();
        $url = route('tenant.incidents.transition', ['incident' => $incident->getKey()]);

        // Note is mandatory for block/dismiss.
        $this->actingAs($owner['user'])->post($url, ['action' => 'blocked'])
            ->assertRedirect()->assertSessionHasErrors('resolution_note');

        $this->actingAs($owner['user'])->post($url, [
            'action' => 'blocked',
            'resolution_note' => 'Vendor denies sending the email.',
        ])->assertRedirect(route('tenant.incidents.show', ['incident' => $incident->getKey()]));

        $this->assertSame('blocked', $incident->refresh()->status);
        $this->assertTrue($vendor->refresh()->payment_hold);

        $this->cleanup($owner['tenant']);
    }

    public function test_needs_info_keeps_incident_open(): void
    {
        $owner = $this->makeTenantOwner('infoco');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Unclear Corp',
        ]);

        $this->actingAs($owner['user'])->post(
            route('tenant.vendors.changes.store', ['vendor' => $vendor->getKey()]),
            ['field_changed' => 'address', 'new_value_hash' => 'hash-x']
        );

        $incident = Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->firstOrFail();

        $this->actingAs($owner['user'])->post(
            route('tenant.incidents.transition', ['incident' => $incident->getKey()]),
            ['action' => 'needs_info', 'resolution_note' => 'Waiting on callback.']
        )->assertRedirect(route('tenant.incidents.show', ['incident' => $incident->getKey()]));

        $incident->refresh();

        $this->assertSame('open', $incident->status);
        $this->assertNull($incident->resolved_at);

        $this->cleanup($owner['tenant']);
    }

    public function test_viewer_cannot_transition_incidents(): void
    {
        $owner = $this->makeTenantOwner('lockco', 'viewer');

        $vendor = Vendor::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'name' => 'Readonly Goods',
        ]);

        $incident = Incident::create([
            'tenant_id' => $owner['tenant']->getKey(),
            'vendor_id' => $vendor->getKey(),
            'status' => 'open',
            'severity' => 'medium',
        ]);

        $this->actingAs($owner['user'])->post(
            route('tenant.incidents.transition', ['incident' => $incident->getKey()]),
            ['action' => 'verified', 'resolution_note' => 'Trying anyway.']
        )->assertForbidden();

        $this->cleanup($owner['tenant']);
    }

    public function test_incidents_are_invisible_to_other_tenants(): void
    {
        $ownerA = $this->makeTenantOwner('secA');
        $ownerB = $this->makeTenantOwner('secB');

        $vendor = Vendor::create([
            'tenant_id' => $ownerA['tenant']->getKey(),
            'name' => 'Secret Payee',
        ]);

        tenancy()->initialize($ownerA['tenant']);
        app(VendorChangeDetectionService::class)->recordChange($vendor, [
            'field_changed' => 'bank_account',
            'new_value_hash' => 'last4:7777',
            'source' => 'manual',
        ]);
        tenancy()->end();

        $incidentId = Incident::withoutTenancy()->where('vendor_id', $vendor->getKey())->value('id');

        // Direct fetch by id: 404, never 403/200.
        $this->actingAs($ownerB['user'])
            ->get(route('tenant.incidents.show', ['incident' => $incidentId]))
            ->assertNotFound();

        // Queue listing excludes it.
        $this->actingAs($ownerB['user'])
            ->get(route('tenant.incidents.index'))
            ->assertOk()
            ->assertDontSee('Secret Payee');

        // Audit rows are tenant-scoped too.
        $this->assertSame(0, AuditTrail::where('tenant_id', $ownerB['tenant']->getKey())->count());

        $this->cleanup($ownerA['tenant'], $ownerB['tenant']);
    }
}
