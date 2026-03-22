<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public static function createEnrollment(array $data): Enrollment
    {
        return DB::transaction(function () use ($data) {
            $classroom = Classroom::with('grade')->findOrFail($data['classroom_id']);
            $gradeId = $data['grade_id'] ?? $classroom->grade_id;
            $termId = $data['term_id'] ?? $classroom->grade->term_id;

            self::validateClassroomGradeTerm($classroom, (int) $gradeId, (int) $termId);

            $current = self::getCurrentEnrollment((int) $data['student_id']);
            if ($current) {
                throw ValidationException::withMessages([
                    'student_id' => 'Student already has a current enrollment. Upgrade or transfer instead.',
                ]);
            }

            $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : (isset($data['enrollment_date']) ? Carbon::parse($data['enrollment_date']) : now());

            return Enrollment::create([
                'student_id' => $data['student_id'],
                'term_id' => $termId,
                'grade_id' => $gradeId,
                'classroom_id' => $classroom->id,
                'status' => 'pending',
                'start_date' => $startDate->toDateString(),
                'enrollment_date' => $startDate->toDateString(),
                'is_current' => true,
            ]);
        });
    }

    public static function closeEnrollment(Enrollment $enrollment, string $status, ?Carbon $endDate = null): Enrollment
    {
        $enrollment->update([
            'status' => $status,
            'is_current' => false,
            'end_date' => ($endDate ?? now())->toDateString(),
        ]);

        return $enrollment->refresh();
    }

    public static function upgradeGrade(Enrollment $currentEnrollment, array $data): Enrollment
    {
        if (!$currentEnrollment->is_current) {
            throw ValidationException::withMessages([
                'enrollment' => 'Only current enrollment can be upgraded.',
            ]);
        }

        return DB::transaction(function () use ($currentEnrollment, $data) {
            self::closeEnrollment($currentEnrollment, 'completed');

            $classroom = Classroom::with('grade')->findOrFail($data['classroom_id']);
            $gradeId = $data['grade_id'] ?? $classroom->grade_id;
            $termId = $data['term_id'] ?? $classroom->grade->term_id;
            self::validateClassroomGradeTerm($classroom, (int) $gradeId, (int) $termId);

            $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : now();

            return Enrollment::create([
                'student_id' => $currentEnrollment->student_id,
                'term_id' => $termId,
                'grade_id' => $gradeId,
                'classroom_id' => $classroom->id,
                'status' => 'pending',
                'start_date' => $startDate->toDateString(),
                'enrollment_date' => $startDate->toDateString(),
                'is_current' => true,
            ]);
        });
    }

    public static function transferClassroom(Enrollment $currentEnrollment, array $data): Enrollment
    {
        if (!$currentEnrollment->is_current) {
            throw ValidationException::withMessages([
                'enrollment' => 'Only current enrollment can be transferred.',
            ]);
        }

        return DB::transaction(function () use ($currentEnrollment, $data) {
            self::closeEnrollment($currentEnrollment, 'transferred');

            $classroom = Classroom::with('grade')->findOrFail($data['classroom_id']);
            $gradeId = $classroom->grade_id;
            $termId = $classroom->grade->term_id;
            self::validateClassroomGradeTerm($classroom, $gradeId, $termId);

            $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : now();

            return Enrollment::create([
                'student_id' => $currentEnrollment->student_id,
                'term_id' => $termId,
                'grade_id' => $gradeId,
                'classroom_id' => $classroom->id,
                'status' => 'pending',
                'start_date' => $startDate->toDateString(),
                'enrollment_date' => $startDate->toDateString(),
                'is_current' => true,
            ]);
        });
    }

    public static function getCurrentEnrollment(int $studentId): ?Enrollment
    {
        return Enrollment::with(['student', 'grade', 'classroom', 'term'])
            ->where('student_id', $studentId)
            ->where('is_current', true)
            ->latest('start_date')
            ->first();
    }

    private static function validateClassroomGradeTerm(Classroom $classroom, int $gradeId, int $termId): void
    {
        if ((int) $classroom->grade_id !== $gradeId) {
            throw ValidationException::withMessages([
                'classroom_id' => 'Classroom does not belong to selected grade.',
            ]);
        }

        if ((int) $classroom->grade->term_id !== $termId) {
            throw ValidationException::withMessages([
                'classroom_id' => 'Classroom grade does not belong to selected term.',
            ]);
        }
    }
}
