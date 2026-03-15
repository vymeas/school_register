<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'tuition_plan_id' => 'required|exists:tuition_plans,id',
            'amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'start_study_date' => 'required|date',
            'payment_method' => 'required|in:cash,aba,acleda,wing',
            'reference_number' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ];
    }
}
