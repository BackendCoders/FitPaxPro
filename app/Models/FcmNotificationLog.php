<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcmNotificationLog extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'fcm_notification_logs';

    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fcmToken(): BelongsTo
    {
        return $this->belongsTo(FcmToken::class);
    }
}
