<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'enrollment_id',
        'tuition_plan_id',
        'amount',
        'payment_date',
        'start_study_date',
        'end_study_date',
        'status',
        'payment_method',
        'reference_number',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'start_study_date' => 'date',
            'end_study_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function tuitionPlan()
    {
        return $this->belongsTo(TuitionPlan::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(PaymentLog::class);
    }
}
