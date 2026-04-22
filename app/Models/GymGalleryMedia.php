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

    protected $appends = ['file_url'];

    protected $casts = [
        'file_size' => 'integer',
        'order_index' => 'integer',
        'is_main_video' => 'boolean',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }
        
        return asset('storage/' . $this->file_path);
    }
}
