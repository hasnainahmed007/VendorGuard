<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Incident;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        Gate::authorize('viewAny', Incident::class);

        $entries = AuditTrail::query()
            ->with(['incident.vendor', 'user'])
            ->when($request->query('action'), fn ($query, $action) => $query->where('action', $action))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        if ($request->query('export') === 'csv') {
            return $this->exportCsv();
        }

        return view('tenant.audit.index', [
            'entries' => $entries,
            'actions' => AuditTrail::ACTIONS,
            'actionFilter' => (string) $request->query('action', ''),
        ]);
    }

    private function exportCsv(): StreamedResponse
    {
        $rows = AuditTrail::query()
            ->with(['incident.vendor', 'user'])
            ->orderBy('id')
            ->cursor();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'created_at', 'incident_id', 'vendor', 'user', 'action', 'note']);

            foreach ($rows as $entry) {
                fputcsv($handle, [
                    $entry->getKey(),
                    $entry->created_at?->toIso8601String(),
                    $entry->incident_id,
                    $entry->incident?->vendor?->name,
                    $entry->user?->email,
                    $entry->action,
                    $entry->note,
                ]);
            }

            fclose($handle);
        }, 'audit-trail.csv', ['Content-Type' => 'text/csv']);
    }
}
