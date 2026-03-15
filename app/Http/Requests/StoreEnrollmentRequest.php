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
            'enrollment_date' => 'required|date',
            'status' => 'nullable|in:active,inactive,transferred',
        ];
    }
}
