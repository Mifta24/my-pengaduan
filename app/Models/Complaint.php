<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'photo',
        'location',
        'priority',
        'status',
        'response',
        'admin_response',
        'estimated_resolution',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
        'estimated_resolution' => 'date',
    ];

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
            'resolved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }
}

