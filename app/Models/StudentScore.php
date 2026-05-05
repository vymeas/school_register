<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    protected $fillable = [
        'student_id',
        'academic_year',
        'term_id',
        'classroom_id',
        'math_score',
        'khmer_score',
        'science_score',
        'sociology_score',
        'remark'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function getTotalAttribute()
    {
        return collect([$this->math_score, $this->khmer_score, $this->science_score, $this->sociology_score])
            ->filter(function ($score) { return $score !== null; })
            ->sum();
    }

    public function getAverageAttribute()
    {
        $scores = collect([$this->math_score, $this->khmer_score, $this->science_score, $this->sociology_score])
            ->filter(function ($score) { return $score !== null; });
            
        return $scores->count() > 0 ? round($scores->sum() / $scores->count(), 2) : 0;
    }

    public function getGradeAttribute()
    {
        $avg = $this->average;
        if ($avg >= 85) return 'A';
        if ($avg >= 70) return 'B';
        if ($avg >= 50) return 'C';
        return 'D';
    }
}
