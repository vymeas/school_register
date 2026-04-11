<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'grade_id',
        'turn_id',
        'teacher_id',
        'name',
        'capacity',
        'is_delete',
    ];

    protected $casts = [
        'is_delete' => 'boolean',
    ];

    /**
     * Global scope: always hide archived classrooms by default.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('active', function (Builder $query) {
            $query->where('is_delete', false);
        });
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function turn()
    {
        return $this->belongsTo(Turn::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
