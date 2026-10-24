<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'category_id',
        'category_slug',
        'badge',
        'rating',
        'reviews_count',
        'users_count',
        'short_desc',
        'description',
        'capabilities',
        'price_monthly',
        'price_lifetime',
        'included_in_sub',
        'engine_version',
        'latency',
        'icon',
        'color',
        'status',
        'is_active',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'included_in_sub' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'float',
        'reviews_count' => 'integer',
        'price_monthly' => 'float',
        'price_lifetime' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if ($product->isDirty('is_active') && ! $product->isDirty('status')) {
                $product->status = $product->is_active ? 'Active' : 'Inactive';
            } elseif ($product->isDirty('status') && ! $product->isDirty('is_active')) {
                $product->is_active = strtolower((string) $product->status) === 'active';
            } elseif ($product->is_active === null && ! empty($product->status)) {
                $product->is_active = strtolower((string) $product->status) === 'active';
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    protected $appends = [
        'priceMonthly',
        'priceLifetime',
        'includedInSub',
        'shortDesc',
        'category',
    ];

    public function getPriceMonthlyAttribute()
    {
        return (float) ($this->attributes['price_monthly'] ?? 0);
    }

    public function getPriceLifetimeAttribute()
    {
        return (float) ($this->attributes['price_lifetime'] ?? 0);
    }

    public function getIncludedInSubAttribute()
    {
        return (bool) ($this->attributes['included_in_sub'] ?? true);
    }

    public function getShortDescAttribute()
    {
        return $this->attributes['short_desc'] ?? '';
    }

    public function getCategoryAttribute()
    {
        if (! empty($this->attributes['category_slug'])) {
            return $this->attributes['category_slug'];
        }

        if ($this->relationLoaded('category') && $this->getRelation('category')) {
            return $this->getRelation('category')->slug;
        }

        return 'llm';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
