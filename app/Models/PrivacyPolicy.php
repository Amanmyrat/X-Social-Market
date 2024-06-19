<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrivacyPolicy
 *
 * @property int $id
 * @property string $content_en
 * @property string $content_ru
 * @property string $content_tk
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PrivacyPolicy newModelQuery()
 * @method static Builder|PrivacyPolicy newQuery()
 * @method static Builder|PrivacyPolicy query()
 * @method static Builder|PrivacyPolicy whereContentEn($value)
 * @method static Builder|PrivacyPolicy whereContentRu($value)
 * @method static Builder|PrivacyPolicy whereContentTk($value)
 * @method static Builder|PrivacyPolicy whereCreatedAt($value)
 * @method static Builder|PrivacyPolicy whereId($value)
 * @method static Builder|PrivacyPolicy whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PrivacyPolicy extends BaseModel
{
    use HasFactory;

    protected $fillable = ['content_en', 'content_ru', 'content_tk'];
}
