<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property int $post_id
 * @property int $brand_id
 * @property string $gender
 * @property array $options
 * @property array $unique_colors
 * @property array $unique_sizes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Brand $brand
 * @property-read Post $post
 *
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereBrandId($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereGender($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereOptions($value)
 * @method static Builder|Product wherePostId($value)
 * @method static Builder|Product whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Product extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'brand_id',
        'gender',
        'options',
        'unique_colors',
        'unique_sizes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'unique_colors' => 'array',
        'unique_sizes' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'product_color');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_size');
    }

    protected function options(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $option = json_decode($value, true);

                $colorIds = collect($option['colors'])->pluck('color_id')->unique();
                $colors = Color::findMany($colorIds)->keyBy('id');

                foreach ($option['colors'] as &$color) {
                    // Fetch the color model just once per color_id to optimize
                    $colorModel = $colors[$color['color_id']] ?? null;
                    if ($colorModel) {
                        $newColor = [
                            'color' => [
                                'id' => $colorModel->id,
                                'title' => $colorModel->title,
                                'code' => $colorModel->code,
                            ],
                        ];

                        // For sizes within each color
                        $sizeIds = collect($color['sizes'])->pluck('size_id')->unique();
                        $sizes = Size::findMany($sizeIds)->keyBy('id');
                        $newSizes = [];
                        foreach ($color['sizes'] as $size) {
                            $sizeModel = $sizes[$size['size_id']] ?? null;
                            if ($sizeModel) {
                                $newSizes[] = [
                                    'size' => [
                                        'id' => $sizeModel->id,
                                        'title' => $sizeModel->title,
                                    ],
                                    'price' => $size['price'],
                                    'stock' => $size['stock'],
                                ];
                            }
                        }
                        $newColor['sizes'] = $newSizes;
                        $color = $newColor;
                    }
                }

                return $option;
            }
        );
    }

    public function getUniqueColorsAttribute(): Collection
    {
        return collect($this->options['colors'] ?? [])->pluck('color')->unique('id')->values();
    }

    public function getUniqueSizesAttribute(): Collection
    {
        return collect($this->options['colors'] ?? [])->flatMap(function ($color) {
            return collect($color['sizes'])->pluck('size');
        })->unique('id')->values();
    }
}
