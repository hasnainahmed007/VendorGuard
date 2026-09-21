<?php

namespace App\Providers;

use App\Services\Alerts\LogWhatsAppSender;
use App\Services\Alerts\WhatsAppSender;
use App\Services\Email\EmailClassifier;
use App\Services\Email\RuleBasedEmailClassifier;
use App\Services\FcmSender;
use App\Services\FirebaseFcmSender;
use App\Services\LocationResolver;
use App\Services\MaxMindLocationResolver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FcmSender::class, FirebaseFcmSender::class);
        $this->app->bind(LocationResolver::class, MaxMindLocationResolver::class);
        $this->app->bind(EmailClassifier::class, RuleBasedEmailClassifier::class);
        $this->app->bind(WhatsAppSender::class, LogWhatsAppSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api-auth', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
