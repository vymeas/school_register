<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'nullable|in:active,inactive',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
        ];
    }
}
