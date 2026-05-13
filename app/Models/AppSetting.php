<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'updated_by',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value, ?int $updatedBy = null): void
    {
        $data = ['value' => $value];
        if ($updatedBy !== null) {
            $data['updated_by'] = $updatedBy;
        }

        static::query()->updateOrCreate(['key' => $key], $data);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
