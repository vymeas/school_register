<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'term_id' => 'nullable|exists:terms,id',
            'registration_date' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:255',
            'photo' => 'nullable|string',
        ];
    }
}
