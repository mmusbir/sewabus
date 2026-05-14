<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleBooking extends Model
{
    protected $fillable = [
        'booking_code',
        'customer_name',
        'customer_phone',
        'departure_from',
        'destination',
        'pickup_location',
        'pickup_time',
        'departure_date',
        'return_date',
        'service_type',
        'service_type_note',
        'po_key',
        'gallery_id',
        'deal_price',
        'markup_price',
        'dp_amount',
        'owner_dp_amount',
        'is_cancelled',
        'is_paid',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'deal_price' => 'decimal:2',
        'markup_price' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'owner_dp_amount' => 'decimal:2',
        'is_cancelled' => 'boolean',
        'is_paid' => 'boolean',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getTotalPriceAttribute(): float
    {
        return (float) $this->markup_price;
    }

    public function getProfitAmountAttribute(): float
    {
        return (float) $this->markup_price - (float) $this->deal_price;
    }

    public function getRemainingAmountAttribute(): float
    {
        if ($this->is_paid || $this->is_cancelled) {
            return 0;
        }

        return max(0, $this->total_price - (float) $this->dp_amount);
    }
}
