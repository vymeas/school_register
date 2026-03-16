<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Term;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        return view('grades.index', [
            'grades' => Grade::active()->with('term')->withCount('classrooms')->orderBy('name')->get(),
            'terms' => Term::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Grade::create($data);

        return redirect()->route('grades.index')->with('success', 'Grade created successfully.');
    }

    public function update(Request $request, Grade $grade)
    {
        $data = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $grade->update($data);

        return redirect()->route('grades.index')->with('success', 'Grade updated successfully.');
    }

    public function restoreIndex()
    {
        return view('settings.grades-restore', [
            'grades' => Grade::where('is_delete', true)->with('term')->withCount('classrooms')->orderBy('name')->get(),
        ]);
    }
}
