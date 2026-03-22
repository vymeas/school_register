<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'study_status',
        'is_delete',
        'registration_date',
        'emergency_contact',
        'emergency_name',
        'photo',
        'teacher_id',
        'start_date',
    ];

    /**
     * Global scope: always hide soft-deleted students.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('not_deleted', function (Builder $query) {
            $query->where('is_delete', false);
        });
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'registration_date' => 'date',
            'start_date' => 'date',
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

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)
            ->where('status', 'paid')
            ->orderByDesc('end_study_date');
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

    public function scopeStudying($query)
    {
        return $query->where('study_status', 'studying');
    }

    public function scopeDropped($query)
    {
        return $query->where('study_status', 'dropped');
    }

    public function scopeOnlyDeleted($query)
    {
        return $query->withoutGlobalScope('not_deleted')->where('is_delete', true);
    }
}
