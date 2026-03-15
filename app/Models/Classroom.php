<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'grade_id',
        'name',
        'capacity',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
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
