<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'user_profiles';

    protected $guarded = [];

    protected $casts = [
        'age' => 'integer',
        'current_weight' => 'decimal:2',
        'height' => 'decimal:2',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
