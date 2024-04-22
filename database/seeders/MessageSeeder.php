<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();

        $chats = Chat::all();

        $chats->each(function ($chat) {
            $messagesCount = rand(10, 100);
            $senderIds = [$chat->sender_user_id, $chat->receiver_user_id];
            $senderIndex = 0;

            $messages = [];

            for ($i = 0; $i < $messagesCount; $i++) {
                $senderId = $senderIds[$senderIndex];
                $receiverId = $senderIds[1 - $senderIndex];
                $body = $this->generateRandomMessage();

                $messages[] = [
                    'chat_id' => $chat->id,
                    'sender_user_id' => $senderId,
                    'receiver_user_id' => $receiverId,
                    'type' => 'message',
                    'body' => Crypt::encrypt($body),
                    'extra' => null,
                    'read_at' => null,
                    'sender_deleted_at' => null,
                    'receiver_deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $senderIndex = 1 - $senderIndex;
            }

            Message::insert($messages);
        });
    }

    private function generateRandomMessage(): string
    {
        $messages = [
            "Hey, how's it going?",
            "What are you up to?",
            "Did you see the latest news?",
            "I have a question for you.",
            "How was your day?",
            "This weather is crazy, right?",
            "I'm so excited about our plans!",
            "Have you watched any good movies lately?",
            "Let's catch up soon!",
            "Remember that time when...",
            "I'm bored, entertain me!",
        ];

        $randomIndex = array_rand($messages);
        return $messages[$randomIndex];
    }
}
