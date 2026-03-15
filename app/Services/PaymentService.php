<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Student;
use App\Models\TuitionPlan;
use Carbon\Carbon;

class PaymentService
{
    /**
     * Create a payment and auto-calculate the end study date.
     */
    public static function createPayment(array $data, string $userId): Payment
    {
        $tuitionPlan = TuitionPlan::findOrFail($data['tuition_plan_id']);
        $startDate = Carbon::parse($data['start_study_date']);
        $endDate = $startDate->copy()->addMonths($tuitionPlan->duration_month);

        $payment = Payment::create([
            'student_id' => $data['student_id'],
            'tuition_plan_id' => $data['tuition_plan_id'],
            'amount' => $data['amount'] ?? $tuitionPlan->price,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'start_study_date' => $startDate->toDateString(),
            'end_study_date' => $endDate->toDateString(),
            'payment_method' => $data['payment_method'] ?? 'cash',
            'reference_number' => $data['reference_number'] ?? null,
            'note' => $data['note'] ?? null,
            'created_by' => $userId,
        ]);

        // Log the payment creation
        PaymentLog::create([
            'payment_id' => $payment->id,
            'action' => 'created',
            'user_id' => $userId,
        ]);

        // Update student status to active
        Student::where('id', $data['student_id'])->update(['status' => 'active']);

        return $payment;
    }

    /**
     * Check and update expired student statuses.
     */
    public static function updateExpiredStudents(): int
    {
        $today = now()->toDateString();

        // Find students whose latest payment has expired
        $expiredStudentIds = Payment::where('end_study_date', '<', $today)
            ->pluck('student_id')
            ->unique();

        $count = 0;
        foreach ($expiredStudentIds as $studentId) {
            // Check if there's any active payment
            $hasActivePayment = Payment::where('student_id', $studentId)
                ->where('end_study_date', '>=', $today)
                ->exists();

            if (!$hasActivePayment) {
                Student::where('id', $studentId)
                    ->where('status', 'active')
                    ->update(['status' => 'expired']);
                $count++;
            }
        }

        return $count;
    }
}
