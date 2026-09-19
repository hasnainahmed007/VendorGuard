<?php

namespace App\Listeners;

use App\Events\IncidentCreated;
use App\Services\Alerts\IncidentAlertService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendIncidentAlerts implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private IncidentAlertService $alerts) {}

    /**
     * Handle the event.
     */
    public function handle(IncidentCreated $event): void
    {
        $this->alerts->dispatchFor($event->incident);
    }
}
