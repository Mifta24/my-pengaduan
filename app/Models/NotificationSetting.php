<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'complaint_created',
        'complaint_status_changed',
        'announcement_created',
        'admin_response',
        'comment_added',
        'push_enabled',
    ];

    protected $casts = [
        'complaint_created' => 'boolean',
        'complaint_status_changed' => 'boolean',
        'announcement_created' => 'boolean',
        'admin_response' => 'boolean',
        'comment_added' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
