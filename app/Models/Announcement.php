<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'cover_image',
        'priority',
        'target_audience',
        'attachments',
        'is_active',
        'is_sticky',  //posisi teratas (pinned)
        'allow_comments',
        'published_at',
        'views_count',
        'author_id',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'is_sticky' => 'boolean',
        'allow_comments' => 'boolean',
        'target_audience' => 'array',
        'attachments' => 'array',
        'views_count' => 'integer'
    ];

    protected $appends = ['status', 'cover_image_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($announcement) {
            if (empty($announcement->slug)) {
                $announcement->slug = Str::slug($announcement->title);
            }
        });

        static::updating(function ($announcement) {
            if ($announcement->isDirty('title')) {
                $announcement->slug = Str::slug($announcement->title);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the author of the announcement
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the creator of the announcement (alias for author)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get comments for this announcement
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)
                    ->where('is_approved', true)
                    ->whereNull('parent_id')
                    ->latest();
    }

    /**
     * Get all comments (including unapproved)
     */
    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Users who bookmarked this announcement
     */
    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'announcement_bookmarks')
                    ->withTimestamps();
    }

    /**
     * Get comments count
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    /**
     * Scope published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope sticky announcements
     */
    public function scopeSticky($query)
    {
        return $query->where('is_sticky', true);
    }

    /**
     * Scope for target audience filtering based on user role
     */
    public function scopeForRole($query, $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->whereNull('target_audience')
              ->orWhereJsonContains('target_audience', 'all')
              ->orWhereJsonContains('target_audience', $role);
        });
    }

    /**
     * Get status attribute
     */
    public function getStatusAttribute()
    {
        if ($this->is_active && $this->published_at && $this->published_at <= now()) {
            return 'published';
        }
        return 'unpublished';
    }

    /**
     * Get cover image URL
     */
    public function getCoverImageUrlAttribute()
    {
        return $this->getAttachmentUrl($this->cover_image);
    }

    /**
     * Get attachment URL - handles both Cloudinary and local storage
     *
     * @param string $path
     * @return string
     */
    public function getAttachmentUrl($path)
    {
        if (!$path) {
            return null;
        }

        // If already a full URL (Cloudinary), return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // For local storage, return full URL with storage path
        return asset('storage/' . $path);
    }
}
