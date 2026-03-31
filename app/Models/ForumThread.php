<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumThread extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'forum_threads';

    protected $guarded = [];

    protected $casts = [
        'is_sticky' => 'boolean',
        'is_locked' => 'boolean',
        'is_verified' => 'boolean',
        'view_count' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }
}
