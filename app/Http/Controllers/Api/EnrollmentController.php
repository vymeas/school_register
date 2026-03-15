<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with(['student', 'classroom.grade', 'term']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->has('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        $enrollments = $query->orderBy('enrollment_date', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($enrollments);
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        $enrollment = Enrollment::create($request->validated());

        return response()->json([
            'message' => 'Enrollment created successfully.',
            'enrollment' => $enrollment->load(['student', 'classroom.grade', 'term']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $enrollment = Enrollment::with(['student', 'classroom.grade', 'term'])->findOrFail($id);

        return response()->json([
            'enrollment' => $enrollment,
        ]);
    }

    public function update(StoreEnrollmentRequest $request, string $id): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update($request->validated());

        return response()->json([
            'message' => 'Enrollment updated successfully.',
            'enrollment' => $enrollment->load(['student', 'classroom.grade', 'term']),
        ]);
    }
}
