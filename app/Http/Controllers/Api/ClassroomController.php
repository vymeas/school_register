<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassroomRequest;
use App\Models\Classroom;
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
        $classroom = Classroom::with(['grade', 'teacher', 'students'])->findOrFail($id);

        return response()->json([
            'classroom' => $classroom,
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
        $classroom->delete();

        return response()->json([
            'message' => 'Classroom deleted successfully.',
        ]);
    }
}
