<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Teacher::with('classroom.grade');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $teachers = $query->orderBy('name')->paginate($request->per_page ?? 15);

        return response()->json($teachers);
    }

    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $teacher = Teacher::create($request->validated());

        return response()->json([
            'message' => 'Teacher created successfully.',
            'teacher' => $teacher->load('classroom.grade'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $teacher = Teacher::with('classroom.grade')->findOrFail($id);

        return response()->json([
            'teacher' => $teacher,
        ]);
    }

    public function update(StoreTeacherRequest $request, string $id): JsonResponse
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update($request->validated());

        return response()->json([
            'message' => 'Teacher updated successfully.',
            'teacher' => $teacher->load('classroom.grade'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully.',
        ]);
    }
}
