<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'media';

    protected $guarded = [];

    protected $casts = [
        'size' => 'integer',
        'order_column' => 'integer',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
