<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Term;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        return view('classrooms.index', [
            'classrooms' => Classroom::with(['grade', 'teacher'])->withCount('students')->orderBy('name')->get(),
            'grades' => Grade::with('term')->orderBy('name')->get(),
            'terms' => Term::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        Classroom::create($data);

        return redirect()->route('classrooms.index')->with('success', 'Classroom created successfully.');
    }
}
