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
        'priority',
        'target_audience',
        'attachments',
        'is_active',
        'is_sticky',  //posisi teratas (pinned)
        'allow_comments',
        'published_at',
        'views_count',
        'author_id'
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

    protected $appends = ['status'];

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

    /**
     * Get comments for this announcement
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
                    ->where('is_approved', true)
                    ->whereNull('parent_id')
                    ->latest();
    }

    /**
     * Get all comments (including unapproved)
     */
    public function allComments()
    {
        return $this->morphMany(Comment::class, 'commentable');
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
     * Get status attribute
     */
    public function getStatusAttribute()
    {
        if ($this->is_active && $this->published_at && $this->published_at <= now()) {
            return 'published';
        }
        return 'unpublished';
    }
}
