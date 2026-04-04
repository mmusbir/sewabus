<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'po_key',
        'category',
        'description',
        'facilities',
        'unit_count',
        'is_active',
    ];

    protected $casts = [
        'unit_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getImagePathAttribute(?string $value): ?string
    {
        return media_url($value);
    }

    public function media(): HasMany
    {
        return $this->hasMany(GalleryMedia::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryMedia::class)->where('type', 'image')->orderBy('sort_order');
    }

    public function video(): HasOne
    {
        return $this->hasOne(GalleryMedia::class)->where('type', 'video');
    }
}
