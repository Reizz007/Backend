<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'badge',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected $appends = [
        'status',
    ];

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (isset($category->attributes['status']) && ! $category->isDirty('is_active')) {
                $category->is_active = strtolower((string) $category->attributes['status']) === 'active';
                unset($category->attributes['status']);
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
