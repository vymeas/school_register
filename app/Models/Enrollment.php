<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'classroom_id',
        'term_id',
        'grade_id',
        'enrollment_date',
        'start_date',
        'end_date',
        'is_current',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
