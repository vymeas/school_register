<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassroomRequest;
use App\Models\Classroom;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Classroom::with(['grade', 'teacher']);

        if ($request->has('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        $classrooms = $query->orderBy('name')->get();

        return response()->json([
            'classrooms' => $classrooms,
        ]);
    }

    public function store(StoreClassroomRequest $request): JsonResponse
    {
        $classroom = Classroom::create($request->validated());

        return response()->json([
            'message' => 'Classroom created successfully.',
            'classroom' => $classroom->load('grade'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $classroom = Classroom::with(['grade.term', 'teacher', 'turn'])->findOrFail($id);
        $termId = $classroom->grade?->term_id;

        $enrollmentStudents = Enrollment::with('student')
            ->where('classroom_id', $classroom->id)
            ->where('grade_id', $classroom->grade_id)
            ->when($termId, function ($query) use ($termId) {
                $query->where('term_id', $termId);
            })
            ->where('is_current', true)
            ->orderByDesc('start_date')
            ->get()
            ->pluck('student')
            ->filter()
            ->unique('id')
            ->values();

        return response()->json([
            'classroom' => $classroom
                ->setAttribute('enrollment_students', $enrollmentStudents)
                ->setAttribute('enrollment_students_count', $enrollmentStudents->count()),
        ]);
    }

    public function update(StoreClassroomRequest $request, string $id): JsonResponse
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update($request->validated());

        return response()->json([
            'message' => 'Classroom updated successfully.',
            'classroom' => $classroom->load('grade'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update(['is_delete' => true]);

        return response()->json([
            'message' => 'Classroom archived successfully.',
        ]);
    }
}
