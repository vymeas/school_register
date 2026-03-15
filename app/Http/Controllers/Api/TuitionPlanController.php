<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TuitionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TuitionPlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = TuitionPlan::orderBy('duration_month')->get();

        return response()->json([
            'tuition_plans' => $plans,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration_month' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        $plan = TuitionPlan::create($data);

        return response()->json([
            'message' => 'Tuition plan created successfully.',
            'tuition_plan' => $plan,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $plan = TuitionPlan::findOrFail($id);

        return response()->json([
            'tuition_plan' => $plan,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration_month' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        $plan = TuitionPlan::findOrFail($id);
        $plan->update($data);

        return response()->json([
            'message' => 'Tuition plan updated successfully.',
            'tuition_plan' => $plan,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $plan = TuitionPlan::findOrFail($id);
        $plan->delete();

        return response()->json([
            'message' => 'Tuition plan deleted successfully.',
        ]);
    }
}
