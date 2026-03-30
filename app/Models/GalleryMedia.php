<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryMedia extends Model
{
    protected $table = 'gallery_media';

    protected $fillable = [
        'gallery_id',
        'type',
        'media_path',
        'sort_order',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }
}
