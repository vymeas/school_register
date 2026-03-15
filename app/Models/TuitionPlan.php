<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionPlan extends Model
{
    protected $fillable = [
        'name',
        'duration_month',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
