<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'term_id' => 'required|exists:terms,id',
            'grade_id' => 'nullable|exists:grades,id',
            'start_date' => 'nullable|date',
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:pending,active,completed,transferred',
        ];
    }
}
