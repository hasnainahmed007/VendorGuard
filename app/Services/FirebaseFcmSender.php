<?php

namespace App\Services;

use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Throwable;

class FirebaseFcmSender implements FcmSender
{
    public function sendToToken(string $token, string $title, string $body, array $data = []): FcmResult
    {
        $credentials = (string) config('firebase.credentials');
        $projectId = (string) config('firebase.project_id');

        if ($credentials === '' || ! is_file($credentials)) {
            return FcmResult::failure('Firebase credentials are not configured.');
        }

        try {
            $factory = (new Factory)->withServiceAccount($credentials);

            if ($projectId !== '') {
                $factory = $factory->withProjectId($projectId);
            }

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body));

            if ($data !== []) {
                $message = $message->withData($data);
            }

            $report = $factory->createMessaging()->send($message);

            $messageId = is_array($report) ? (string) ($report['name'] ?? '') : '';

            return FcmResult::success($messageId);
        } catch (NotFound $exception) {
            return FcmResult::failure($exception->getMessage(), true);
        } catch (Throwable $exception) {
            report($exception);

            return FcmResult::failure($exception->getMessage());
        }
    }
}
