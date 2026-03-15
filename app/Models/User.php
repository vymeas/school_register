<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'role',
        'status',
        'phone',
        'email',
        'is_deleted',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_deleted' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Relationships
    public function payments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_deleted', false);
    }
}
