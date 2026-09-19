<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Billing\BillingService;
use App\Services\Billing\StripeClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeClient $stripe, BillingService $billing): JsonResponse
    {
        $signature = (string) $request->header('Stripe-Signature', '');
        $payload = $request->getContent();

        if (! $stripe->verifySignature($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return response()->json(['message' => 'Invalid payload.'], 422);
        }

        $billing->handleWebhookEvent($event);

        return response()->json(['received' => true]);
    }
}
