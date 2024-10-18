<?php

namespace App\Services;

use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Http;
use Google\Client;

class FirebaseNotificationService
{
    public static function sendFirebaseNotification(int $notificationId, string $deviceToken)
    {
        $notification = Notification::find($notificationId);
        $serviceAccountPath = base_path('tanat-firebase-adminsdk.json');
        if (file_exists($serviceAccountPath)) {

            $projectId = 'tanat-d9979';

            $message = [
                'token' => $deviceToken,
                'notification' => [
                    'title' => 'Tanat',
                    'body' => 'Test',
                ],
                'data' => self::generateNotificationContent($notification)
            ];

            try {
                $accessToken = self::getAccessToken($serviceAccountPath);
                $response = self::sendMessage($accessToken, $projectId, $message);
                echo 'Message sent successfully: ' . print_r($response, true);
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }

        } else {
            abort(404, 'File not found');
        }
    }

    private static function generateNotificationContent(Notification $notification): array
    {
        $result = [
            'notification_type' => $notification->type,
            'user' => $notification->initiator != null ? [
                'id' => $notification->initiator->id,
                'username' => $notification->initiator->username,
                'full_name' => $notification->initiator->profile?->full_name,
                'image' => $notification->initiator->profile?->image_urls,
            ] : null,
            'reason' => $notification->reason,
            'created_at' => $notification->created_at,
        ];

        if ($notification->post_id != null) {
            $result += [
                'post' => [
                    'id' => $notification->post->id,
                    'media' => $notification->post->first_image_urls,
                ]
            ];
        } else if ($notification->story_id != null) {
            $result += [
                'story' => [
                    'id' => $notification->story->id,
                    'content' => $notification->story->image_urls,
                ],
            ];
        }

        return $result;
    }

    /**
     * @throws \Google\Exception
     */
    private static function getAccessToken($serviceAccountPath)
    {
        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    /**
     * @throws Exception
     */
    private static function sendMessage($accessToken, $projectId, $message)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'message' => $message,
            ]);

        if ($response->failed()) {
            throw new Exception('HTTP request failed: ' . $response->body());
        }

        return $response->json();
    }
}
