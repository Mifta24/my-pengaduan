<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'nik',
        'ktp_path',
        'phone',
        'rt_number',
        'rw_number',
        'is_verified',
        'verified_at',
        'is_active',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Append custom attributes
     */
    protected $appends = ['ktp_url'];

    /**
     * Get KTP URL accessor
     */
    public function getKtpUrlAttribute()
    {
        if (!$this->ktp_path) {
            return null;
        }

        // If already a full URL, return as is
        if (filter_var($this->ktp_path, FILTER_VALIDATE_URL)) {
            return $this->ktp_path;
        }

        // Return full URL with storage path
        return url('storage/' . $this->ktp_path);
    }

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'alamat'])
            ->logOnlyDirty();
    }

    /**
     * Relationships
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function notificationSettings()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function fcmNotifications()
    {
        return $this->hasMany(FcmNotification::class);
    }

    /**
     * Bookmarked announcements
     */
    public function bookmarkedAnnouncements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_bookmarks')
                    ->withTimestamps();
    }

    /**
     * Get all active device tokens for this user
     */
    public function getActiveDeviceTokens()
    {
        return $this->devices()
            ->where('is_active', true)
            ->pluck('device_token')
            ->toArray();
    }

    /**
     * Helper methods
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
