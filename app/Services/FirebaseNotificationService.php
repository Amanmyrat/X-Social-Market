<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Http;
use Google\Client;

class FirebaseNotificationService
{
    public function sendFirebaseNotification(int $notificationId, string $deviceToken)
    {
        $notification = Notification::find($notificationId);
        $serviceAccountPath = base_path('tanat-firebase-adminsdk.json');
        if (file_exists($serviceAccountPath)) {

            $projectId = 'tanat-d9979';

            $message = [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $notification->initiator->username ?? '',
                    'body' => $notification->type,
                ],
                'data' => $this->generateNotificationContent($notification)
            ];

            try {
                $accessToken = $this->getAccessToken($serviceAccountPath);
                $response = $this->sendMessage($accessToken, $projectId, $message);
                echo 'Message sent successfully: ' . print_r($response, true);
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }

        } else {
            abort(404, 'File not found');
        }
    }

    public function sendFirebaseMessageNotification(Message $message, string $deviceToken)
    {
        $serviceAccountPath = base_path('tanat-firebase-adminsdk.json');
        if (file_exists($serviceAccountPath)) {

            $projectId = 'tanat-d9979';

            $message = [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $message->sender->username ?? '',
                    'body' => $message->body ?? '',
                ],
                'data' => $this->generateMessageNotificationContent($message)
            ];

            try {
                $accessToken = $this->getAccessToken($serviceAccountPath);
                $response = $this->sendMessage($accessToken, $projectId, $message);
                echo 'Message sent successfully: ' . print_r($response, true);
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }

        } else {
            abort(404, 'File not found');
        }
    }

    private function generateNotificationContent(Notification $notification): array
    {
        $data = [
            'notification_type' => $notification->type->value,
            'reason' => $notification->reason,
            'created_at' => $notification->created_at,
        ];

        if ($notification->initiator) {
            $data['user_id'] = (string)$notification->initiator->id;
            $data['user_username'] = $notification->initiator->username;
            $data['user_full_name'] = $notification->initiator->profile?->full_name ?? '';
            $data['user_image'] = $notification->initiator->profile?->image_urls['medium_url'] ?? '';
        }

        if ($notification->post_id) {
            $data['post_id'] = (string)$notification->post->id;
            $data['post_media'] = $notification->post->first_image_urls['medium_url'] ?? '';
        } elseif ($notification->story_id) {
            $data['story_id'] = (string)$notification->story->id;
            $data['story_content'] = $notification->story->image_urls['medium_url'] ?? '';
        }

        return array_map('strval', $data);
    }

    private function generateMessageNotificationContent(Message $message): array
    {
        $data = [
            'type' => $message->type,
            'body' => $message->body,
            'image' => $message->sender->profile->image_urls['medium_url'] ?? '',
            'created_at' => $message->created_at,
        ];

        return array_map('strval', $data);
    }


    /**
     * @throws \Google\Exception
     */
    private function getAccessToken($serviceAccountPath)
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
    private function sendMessage($accessToken, $projectId, $message)
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
