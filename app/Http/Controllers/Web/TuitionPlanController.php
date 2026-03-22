<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TuitionPlan;
use Illuminate\Http\Request;

class TuitionPlanController extends Controller
{
    public function index()
    {
        return view('tuition-plans.index', [
            'plans' => TuitionPlan::orderBy('duration_month')->get(),
            'classrooms' => \App\Models\Classroom::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration_month' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'classroom' => 'nullable|string|max:255',
        ]);

        TuitionPlan::create($data);

        return redirect()->route('tuition-plans.index')->with('success', 'Tuition plan created successfully.');
    }
}
