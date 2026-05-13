<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'updated_by',
        'name',
        'description',
        'icon',
        'color',
        'is_active',
    ];

    // Note: slug field is sent from client but not stored in database

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper methods
     */
    public function getComplaintsCountAttribute()
    {
        return $this->complaints()->count();
    }

    public function getIconClassAttribute()
    {
        return $this->icon ?: 'folder';
    }
}
