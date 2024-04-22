<?php

namespace App\Models;

use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
 * @property int|null $post_id
 * @property-read Collection<int, Message> $messages
 * @property-read int|null $messages_count
 * @property-read Post|null $post
 *
 * @method static Builder|Chat newModelQuery()
 * @method static Builder|Chat newQuery()
 * @method static Builder|Chat query()
 * @method static Builder|Chat whereCreatedAt($value)
 * @method static Builder|Chat whereDeletedAt($value)
 * @method static Builder|Chat whereId($value)
 * @method static Builder|Chat whereNotDeleted()
 * @method static Builder|Chat wherePostId($value)
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
        'post_id',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class)->withDefault();
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
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
}
