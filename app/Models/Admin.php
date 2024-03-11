<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static Builder|Admin newModelQuery()
 * @method static Builder|Admin newQuery()
 * @method static Builder|Admin query()
 * @method static Builder|Admin whereCreatedAt($value)
 * @method static Builder|Admin whereEmail($value)
 * @method static Builder|Admin whereId($value)
 * @method static Builder|Admin whereIsSuper($value)
 * @method static Builder|Admin whereName($value)
 * @method static Builder|Admin wherePassword($value)
 * @method static Builder|Admin whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles;

    protected string $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'phone',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];
}
