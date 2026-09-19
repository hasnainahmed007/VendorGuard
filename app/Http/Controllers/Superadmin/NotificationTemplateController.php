<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:notifications.read')->only('index');
        $this->middleware('permission:notifications.create')->only(['create', 'store']);
        $this->middleware('permission:notifications.edit')->only(['edit', 'update']);
        $this->middleware('permission:notifications.delete')->only('destroy');
    }

    public function index(): View
    {
        $templates = NotificationTemplate::query()
            ->latest()
            ->orderByDesc('id')
            ->paginate();

        return view('superadmin.notifications.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('superadmin.notifications.templates.create', [
            'types' => NotificationTemplate::TYPES,
            'channels' => NotificationTemplate::CHANNELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', NotificationTemplate::TYPES)],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
            'channel' => ['required', 'string', 'in:'.implode(',', NotificationTemplate::CHANNELS)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        NotificationTemplate::create($validated + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('superadmin.notifications.templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(NotificationTemplate $template): View
    {
        return view('superadmin.notifications.templates.edit', [
            'template' => $template,
            'types' => NotificationTemplate::TYPES,
            'channels' => NotificationTemplate::CHANNELS,
        ]);
    }

    public function update(Request $request, NotificationTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', NotificationTemplate::TYPES)],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
            'channel' => ['required', 'string', 'in:'.implode(',', NotificationTemplate::CHANNELS)],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $template->update($validated + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('superadmin.notifications.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(NotificationTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('superadmin.notifications.templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
