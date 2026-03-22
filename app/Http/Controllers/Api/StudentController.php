<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use App\Services\StudentCodeGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['classroom.grade', 'term']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->has('term_id')) {
            $query->where('term_id', $request->term_id);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($students);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['student_code'] = StudentCodeGenerator::generate();
        $data['registration_date'] = $data['registration_date'] ?? now()->toDateString();

        $student = Student::create($data);

        return response()->json([
            'message' => 'Student created successfully.',
            'student' => $student->load(['classroom.grade', 'term']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $student = Student::with(['classroom.grade', 'term', 'enrollments', 'payments.tuitionPlan'])->findOrFail($id);

        return response()->json([
            'student' => $student,
        ]);
    }

    public function update(StoreStudentRequest $request, string $id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $student->update($request->validated());

        return response()->json([
            'message' => 'Student updated successfully.',
            'student' => $student->load(['classroom.grade', 'term']),
        ]);
    }

    public function generateCode(): JsonResponse
    {
        return response()->json([
            'code' => StudentCodeGenerator::generate(),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $student->update(['is_delete' => true]);

        return response()->json([
            'message' => 'Student deleted successfully.',
        ]);
    }

    public function restore(string $id): JsonResponse
    {
        $student = Student::onlyDeleted()->findOrFail($id);
        $student->update(['is_delete' => false]);

        return response()->json([
            'message' => 'Student restored successfully.',
        ]);
    }
}
