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
        'itinerary',
        'excludes',
        'terms_conditions',
        'image_path',
        'vehicle_exterior_image_path',
        'vehicle_interior_image_path',
        'lodging_exterior_image_path',
        'lodging_interior_image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'itinerary' => 'array',
    ];

    public function getImagePathAttribute(?string $value): ?string
    {
        return media_url($value);
    }

    public function getVehicleExteriorImagePathAttribute(?string $value): ?string
    {
        return media_url($value);
    }

    public function getVehicleInteriorImagePathAttribute(?string $value): ?string
    {
        return media_url($value);
    }

    public function getLodgingExteriorImagePathAttribute(?string $value): ?string
    {
        return media_url($value);
    }

    public function getLodgingInteriorImagePathAttribute(?string $value): ?string
    {
        return media_url($value);
    }
}
