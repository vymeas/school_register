<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
