<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(): JsonResponse
    {
        $terms = Term::orderBy('start_date', 'desc')->get();

        return response()->json([
            'terms' => $terms,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:active,inactive',
        ]);

        $term = Term::create($data);

        return response()->json([
            'message' => 'Term created successfully.',
            'term' => $term,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $term = Term::findOrFail($id);

        return response()->json([
            'term' => $term,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:active,inactive',
        ]);

        $term = Term::findOrFail($id);
        $term->update($data);

        return response()->json([
            'message' => 'Term updated successfully.',
            'term' => $term,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $term = Term::findOrFail($id);
        $term->delete();

        return response()->json([
            'message' => 'Term deleted successfully.',
        ]);
    }
}
