<?php

use App\Jobs\SyncGmailMessages;
use App\Jobs\SyncQuickBooksVendors;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncQuickBooksVendors)->hourly();
Schedule::job(new SyncGmailMessages)->everyFifteenMinutes();
