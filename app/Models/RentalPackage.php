<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPackage extends Model
{
    protected $fillable = [
        'title',
        'type',
        'price_label',
        'duration',
        'description',
        'includes',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
