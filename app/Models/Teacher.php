<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'classroom_id',
        'status',
        'address',
        'hire_date',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
