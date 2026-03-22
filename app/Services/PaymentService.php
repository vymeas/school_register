<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Student;
use App\Models\TuitionPlan;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public static function createPayment(array $data, string $userId): Payment
    {
        $enrollment = Enrollment::findOrFail($data['enrollment_id']);

        if ((int) $enrollment->student_id !== (int) $data['student_id']) {
            throw ValidationException::withMessages([
                'enrollment_id' => 'Enrollment does not belong to the selected student.',
            ]);
        }

        $tuitionPlan = TuitionPlan::findOrFail($data['tuition_plan_id']);
        $expectedAmount = number_format((float) $tuitionPlan->price, 2, '.', '');
        $receivedAmount = number_format((float) $data['amount'], 2, '.', '');

        if ($expectedAmount !== $receivedAmount) {
            throw ValidationException::withMessages([
                'amount' => "Payment amount must match tuition plan price ({$expectedAmount}).",
            ]);
        }

        $paymentDate = isset($data['payment_date']) ? Carbon::parse($data['payment_date']) : now();
        $lastPayment = self::getLastPayment((int) $data['enrollment_id']);
        $startDate = self::calculateStartDate($lastPayment, $paymentDate);
        $endDate = self::calculateEndDate($startDate, (int) $tuitionPlan->duration_month);

        $payment = Payment::create([
            'student_id' => $data['student_id'],
            'enrollment_id' => $data['enrollment_id'],
            'tuition_plan_id' => $data['tuition_plan_id'],
            'amount' => $receivedAmount,
            'payment_date' => $paymentDate->toDateString(),
            'start_study_date' => $startDate->toDateString(),
            'end_study_date' => $endDate->toDateString(),
            'status' => 'paid',
            'payment_method' => $data['payment_method'] ?? 'cash',
            'reference_number' => $data['reference_number'] ?? null,
            'note' => $data['note'] ?? null,
            'created_by' => $userId,
        ]);

        PaymentLog::create([
            'payment_id' => $payment->id,
            'action' => 'created',
            'user_id' => $userId,
        ]);

        self::syncStudentClassroomFromEnrollment($enrollment);
        self::updateEnrollmentStatus((int) $enrollment->id);
        self::updateStudentStatus((int) $data['student_id']);

        return $payment;
    }

    public static function getLastPayment(int $enrollmentId): ?Payment
    {
        return Payment::where('enrollment_id', $enrollmentId)
            ->where('status', 'paid')
            ->latest('end_study_date')
            ->first();
    }

    public static function calculateStartDate(?Payment $lastPayment, Carbon $paymentDate): Carbon
    {
        if (!$lastPayment) {
            return $paymentDate->copy()->startOfDay();
        }

        return Carbon::parse($lastPayment->end_study_date)->startOfDay();
    }

    public static function calculateEndDate(Carbon $startDate, int $durationMonth): Carbon
    {
        return $startDate->copy()->addMonths($durationMonth);
    }

    public static function updateEnrollmentStatus(int $enrollmentId): void
    {
        Enrollment::whereKey($enrollmentId)->update(['status' => 'active']);
    }

    public static function syncStudentClassroomFromEnrollment(Enrollment $enrollment): void
    {
        Student::whereKey($enrollment->student_id)->update([
            'classroom_id' => $enrollment->classroom_id,
            'term_id' => $enrollment->term_id,
        ]);
    }

    public static function updateStudentStatus(int $studentId): void
    {
        $latestPayment = Payment::where('student_id', $studentId)
            ->where('status', 'paid')
            ->latest('end_study_date')
            ->first();

        if (!$latestPayment) {
            Student::whereKey($studentId)->update(['status' => 'pending']);
            return;
        }

        $today = now()->startOfDay();
        $paidUntil = Carbon::parse($latestPayment->end_study_date)->startOfDay();
        $status = $today->lte($paidUntil) ? 'active' : 'expired';

        Student::whereKey($studentId)->update(['status' => $status]);
    }

    public static function updateExpiredStudents(): int
    {
        $count = 0;
        $studentIds = Payment::where('status', 'paid')->pluck('student_id')->unique();

        foreach ($studentIds as $studentId) {
            $before = Student::whereKey($studentId)->value('status');
            self::updateStudentStatus((int) $studentId);
            $after = Student::whereKey($studentId)->value('status');
            if ($before !== $after) {
                $count++;
            }
        }

        return $count;
    }
}
