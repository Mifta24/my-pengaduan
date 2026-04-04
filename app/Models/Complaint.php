<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Complaint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'photo',
        'location',
        'priority',
        'status',
        'admin_resolved_at',
        'user_resolved_at',
        'auto_resolve_at',
        'resolved_at',
        'resolved_by',
        'estimated_resolution',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'datetime',
        'estimated_resolution' => 'datetime',
        'admin_resolved_at' => 'datetime',
        'user_resolved_at' => 'datetime',
        'auto_resolve_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Append custom attributes
     */
    protected $appends = ['photo_url'];

    /**
     * Get photo URL accessor
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }

        // If already a full URL, return as is
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        // Return full URL with storage path
        return url('storage/' . $this->photo);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Compatibility accessor to keep existing views/API payload stable.
     */
    public function getAdminResponseAttribute()
    {
        return $this->responses()
            ->where('user_id', '!=', $this->user_id)
            ->latest('created_at')
            ->value('content');
    }

    /**
     * Legacy alias for old `response` column.
     */
    public function getResponseAttribute()
    {
        return $this->admin_response;
    }

    /**
     * Legacy timestamp alias for old `responded_at` field usage in views.
     */
    public function getRespondedAtAttribute()
    {
        $respondedAt = $this->responses()
            ->where('user_id', '!=', $this->user_id)
            ->latest('created_at')
            ->value('created_at');

        return $respondedAt ? Carbon::parse($respondedAt) : null;
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function complaintAttachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')
                    ->where('attachment_type', 'complaint');
    }

    public function resolutionAttachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')
                    ->where('attachment_type', 'resolution');
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'waiting_user_confirmation' => 'orange',
            'resolved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }
}

