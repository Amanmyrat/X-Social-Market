<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Chat
 *
 * @property int $id
 * @property int $sender_user_id
 * @property int $receiver_user_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Message> $messages
 * @property-read int|null $messages_count
 *
 * @method static Builder|Chat newModelQuery()
 * @method static Builder|Chat newQuery()
 * @method static Builder|Chat query()
 * @method static Builder|Chat whereCreatedAt($value)
 * @method static Builder|Chat whereDeletedAt($value)
 * @method static Builder|Chat whereId($value)
 * @method static Builder|Chat whereNotDeleted()
 * @method static Builder|Chat whereReceiverUserId($value)
 * @method static Builder|Chat whereSenderUserId($value)
 * @method static Builder|Chat whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_user_id',
        'receiver_user_id',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
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

    public function scopeWhereNotDeleted($query): mixed
    {
        $userId = auth()->id();

        return $query->where(function ($query) use ($userId) {

            //where message is not deleted
            $query->whereHas('messages', function ($query) use ($userId) {
                $query->where(function ($query) use ($userId) {
                    $query->where('sender_user_id', $userId)
                        ->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {
                    $query->where('receiver_user_id', $userId)
                        ->whereNull('receiver_deleted_at');
                });
            })
                //include conversations without messages
                ->orWhereDoesntHave('messages');
        });
    }

    public function unreadMessagesCount(): int
    {
        /** @var User $user */
        $user = Auth::user();

        return Message::where('chat_id', $this->id)
            ->where('receiver_user_id', $user->id)
            ->whereNull('read_at')->count();
    }
}
