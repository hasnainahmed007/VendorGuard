<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransitionIncidentRequest;
use App\Models\Incident;
use App\Services\IncidentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Incident::class);

        $status = $request->query('status', 'open');

        $incidents = Incident::query()
            ->with(['vendor', 'changeLog'])
            ->when(in_array($status, Incident::STATUSES, true), fn ($query) => $query->ofStatus($status))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $counts = Incident::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        return view('tenant.incidents.index', [
            'incidents' => $incidents,
            'status' => $status,
            'statuses' => Incident::STATUSES,
            'counts' => $counts,
        ]);
    }

    public function show(Incident $incident): View
    {
        $this->ensureTenantModel($incident);
        Gate::authorize('view', $incident);

        $incident->load(['vendor', 'changeLog', 'assignee', 'auditEntries.user']);

        return view('tenant.incidents.show', ['incident' => $incident]);
    }

    public function transition(
        TransitionIncidentRequest $request,
        Incident $incident,
        IncidentService $incidents
    ): RedirectResponse {
        $this->ensureTenantModel($incident);
        Gate::authorize('transition', $incident);

        $input = $request->validated();

        $incidents->transition(
            $incident,
            $input['action'],
            $request->user(),
            $input['resolution_note'] ?? null,
            $input['assigned_to_user_id'] ?? null
        );

        return redirect()
            ->to(route('tenant.incidents.show', ['incident' => $incident->getKey()]))
            ->with('success', 'Incident updated.');
    }
}
