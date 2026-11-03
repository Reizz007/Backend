<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopupGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_game_id',
        'code',
        'name',
        'icon_url',
        'is_active',
    ];

    protected $casts = [
        'api_game_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function packages(): HasMany
    {
        return $this->hasMany(TopupPackage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(TopupOrder::class);
    }
}
