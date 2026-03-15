<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'term_id',
        'name',
        'description',
        'is_delete',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_delete', false);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
