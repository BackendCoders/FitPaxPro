<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymGalleryMedia extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'gym_gallery_media';

    protected $guarded = [];

    protected $casts = [
        'file_size' => 'integer',
        'order_index' => 'integer',
        'is_main_video' => 'boolean',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function getFilePathAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }
}
