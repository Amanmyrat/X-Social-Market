<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Chat
 *
 * @mixin Eloquent
 * @property int id
 * @property int sender_user_id
 * @property int receiver_user_id
 * @property string deleted_at
 * @property string created_at
 * @property string updated_at
 */
class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_user_id',
        'receiver_user_id'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getReceiver(): Model|User|null
    {
        if ($this->sender_user_id === auth()->id()) {
            return User::firstWhere('id', $this->receiver_user_id);
        } else {
            return User::firstWhere('id', $this->sender_user_id);
        }
    }

    public function scopeWhereNotDeleted($query)
    {
        $userId = auth()->id();

        return $query->where(function ($query) use ($userId) {

            #where message is not deleted
            $query->whereHas('messages', function ($query) use ($userId) {
                $query->where(function ($query) use ($userId) {
                    $query->where('sender_user_id', $userId)
                        ->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {
                    $query->where('receiver_user_id', $userId)
                        ->whereNull('receiver_deleted_at');
                });
            })
                #include conversations without messages
                ->orWhereDoesntHave('messages');
        });
    }

    public function unreadMessagesCount(): int
    {
        /** @var User $user */
        $user = auth()->user();

        return Message::where('chat_id', $this->id)
            ->where('receiver_user_id', $user->id)
            ->whereNull('read_at')->count();
    }
}
