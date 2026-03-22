<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Turn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TurnController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'turns' => Turn::orderBy('start_time')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $turn = Turn::create($data);

        return response()->json([
            'message' => 'Turn created successfully.',
            'turn' => $turn,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $turn = Turn::findOrFail($id);
        return response()->json(['turn' => $turn]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $turn = Turn::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $turn->update($data);

        return response()->json([
            'message' => 'Turn updated successfully.',
            'turn' => $turn,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $turn = Turn::findOrFail($id);
        
        if ($turn->classrooms()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete Turn that is assigned to classrooms.',
            ], 422);
        }

        $turn->delete();

        return response()->json([
            'message' => 'Turn deleted successfully.',
        ]);
    }
}
