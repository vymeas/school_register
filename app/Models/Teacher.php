<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'teacher_code',
        'name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'classroom_id',
        'status',
        'address',
        'hire_date',
        'is_delete',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'date_of_birth' => 'date',
        ];
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_delete', false);
    }
}
