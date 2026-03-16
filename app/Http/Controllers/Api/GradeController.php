<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(): JsonResponse
    {
        $grades = Grade::active()->with(['term', 'classrooms'])->orderBy('name')->get();

        return response()->json([
            'grades' => $grades,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $grade = Grade::create($data);

        return response()->json([
            'message' => 'Grade created successfully.',
            'grade' => $grade,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $grade = Grade::active()->with(['term', 'classrooms.teacher'])->findOrFail($id);

        return response()->json([
            'grade' => $grade,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $grade = Grade::findOrFail($id);
        $grade->update($data);

        return response()->json([
            'message' => 'Grade updated successfully.',
            'grade' => $grade,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $grade = Grade::findOrFail($id);
        $grade->update(['is_delete' => true]);

        return response()->json([
            'message' => 'Grade deleted successfully.',
        ]);
    }

    public function restore(string $id): JsonResponse
    {
        $grade = Grade::findOrFail($id);
        $grade->update(['is_delete' => false]);

        return response()->json([
            'message' => 'Grade restored successfully.',
        ]);
    }
}
