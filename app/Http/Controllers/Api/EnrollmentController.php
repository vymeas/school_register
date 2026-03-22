<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\Student;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with(['student', 'classroom.grade', 'term', 'grade']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->has('term_id')) {
            $query->where('term_id', $request->term_id);
        }
        if ($request->has('is_current')) {
            $query->where('is_current', filter_var($request->is_current, FILTER_VALIDATE_BOOLEAN));
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($enrollments);
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        $enrollment = EnrollmentService::createEnrollment($request->validated());

        return response()->json([
            'message' => 'Enrollment created successfully.',
            'enrollment' => $enrollment->load(['student', 'classroom.grade', 'term', 'grade']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $enrollment = Enrollment::with(['student', 'classroom.grade', 'term', 'grade', 'payments.tuitionPlan'])->findOrFail($id);

        return response()->json([
            'enrollment' => $enrollment,
        ]);
    }

    public function update(StoreEnrollmentRequest $request, string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Direct enrollment update is not allowed. Use upgrade or transfer flow.',
        ], 422);
    }

    public function upgrade(Request $request, Enrollment $enrollment): JsonResponse
    {
        $payload = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'grade_id' => 'required|exists:grades,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'start_date' => 'nullable|date',
        ]);

        $newEnrollment = EnrollmentService::upgradeGrade($enrollment, $payload);

        return response()->json([
            'message' => 'Enrollment upgraded successfully.',
            'previous' => $enrollment->refresh(),
            'current' => $newEnrollment->load(['student', 'classroom.grade', 'term', 'grade']),
        ]);
    }

    public function transfer(Request $request, Enrollment $enrollment): JsonResponse
    {
        $payload = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'start_date' => 'nullable|date',
        ]);

        $newEnrollment = EnrollmentService::transferClassroom($enrollment, $payload);

        return response()->json([
            'message' => 'Enrollment transferred successfully.',
            'previous' => $enrollment->refresh(),
            'current' => $newEnrollment->load(['student', 'classroom.grade', 'term', 'grade']),
        ]);
    }

    public function history(Student $student): JsonResponse
    {
        $history = Enrollment::with(['classroom.grade', 'term', 'grade', 'payments.tuitionPlan'])
            ->where('student_id', $student->id)
            ->latest('created_at')
            ->get();

        return response()->json([
            'student' => $student,
            'history' => $history,
        ]);
    }

    public function current(Student $student): JsonResponse
    {
        $current = EnrollmentService::getCurrentEnrollment($student->id);

        return response()->json([
            'student' => $student,
            'current' => $current,
        ]);
    }
}
