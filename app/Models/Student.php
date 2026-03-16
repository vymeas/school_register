<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_code',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'parent_name',
        'parent_phone',
        'father_name',
        'father_contact',
        'mother_name',
        'mother_contact',
        'address',
        'characteristics',
        'health',
        'classroom_id',
        'term_id',
        'turn',
        'time',
        'status',
        'registration_date',
        'emergency_contact',
        'emergency_name',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'registration_date' => 'date',
        ];
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationships
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
