<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorChangeRequest;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\VerifyVendorRequest;
use App\Models\Vendor;
use App\Services\Email\EmailClassifier;
use App\Services\VendorChangeDetectionService;
use App\Services\VerificationService;
use App\Support\BankDetailHasher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Vendor::class);

        $vendors = Vendor::query()
            ->when($request->query('verified') === '1', fn ($query) => $query->verified())
            ->when($request->query('verified') === '0', fn ($query) => $query->unverified())
            ->when($request->query('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('tenant.vendors.index', [
            'vendors' => $vendors,
            'search' => (string) $request->query('search', ''),
            'verifiedFilter' => (string) $request->query('verified', ''),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Vendor::class);

        return view('tenant.vendors.create');
    }

    public function store(
        StoreVendorRequest $request,
        VendorChangeDetectionService $detection,
        EmailClassifier $classifier
    ): RedirectResponse {
        $input = $request->validated();

        $vendor = Vendor::create([
            'tenant_id' => tenant()->getTenantKey(),
            'provider' => $input['provider'] ?? 'manual',
            'external_id' => $input['external_id'] ?? null,
            'name' => $input['name'],
            'verified_phone' => $input['verified_phone'] ?? null,
            'verified_at' => isset($input['verified_phone']) ? now() : null,
            'verified_by_user_id' => isset($input['verified_phone']) ? $request->user()->getKey() : null,
            'current_bank_last4' => BankDetailHasher::last4($input['bank_account'] ?? null),
            'current_routing_hash' => BankDetailHasher::hash($input['routing_number'] ?? null),
        ]);

        if (! empty($input['invoice_note'])) {
            $detection->flagNewVendor($vendor, $input['invoice_note'], $classifier);
        }

        return redirect()
            ->to(route('tenant.vendors.verify', ['vendor' => $vendor->getKey()]))
            ->with('success', 'Vendor added. Confirm the trusted callback number to finish verification.');
    }

    public function verifyForm(Vendor $vendor): View
    {
        $this->ensureTenantModel($vendor);
        Gate::authorize('verify', $vendor);

        return view('tenant.vendors.verify', ['vendor' => $vendor]);
    }

    public function verify(
        VerifyVendorRequest $request,
        Vendor $vendor,
        VerificationService $verification
    ): RedirectResponse {
        $this->ensureTenantModel($vendor);
        Gate::authorize('verify', $vendor);

        $verification->verifyVendor($vendor, $request->validated()['verified_phone'], $request->user());

        return redirect()
            ->to(route('tenant.vendors.index'))
            ->with('success', "Callback number verified for {$vendor->name}.");
    }

    /**
     * Manually record a detected change (the same service the QuickBooks
     * and email watchers call). Always opens an incident.
     */
    public function recordChange(
        StoreVendorChangeRequest $request,
        Vendor $vendor,
        VendorChangeDetectionService $detection
    ): RedirectResponse {
        $this->ensureTenantModel($vendor);
        Gate::authorize('update', $vendor);

        $incident = $detection->recordChange($vendor, $request->validated());

        return redirect()
            ->to(route('tenant.incidents.show', ['incident' => $incident->getKey()]))
            ->with('success', 'Change recorded. Payment is on hold pending verification.');
    }

    /**
     * Archive the vendor (soft delete only — fraud history is kept).
     */
    public function destroy(Vendor $vendor): RedirectResponse
    {
        $this->ensureTenantModel($vendor);
        Gate::authorize('delete', $vendor);

        $vendor->delete();

        return redirect()
            ->to(route('tenant.vendors.index'))
            ->with('success', "Vendor {$vendor->name} archived.");
    }
}
