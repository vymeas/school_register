<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function index()
    {
        // For simple display, grab latest score if any
        $students = \App\Models\Student::with(['scores' => function($q) {
            $q->latest();
        }])->active()->paginate(10);
        
        return view('scores.index', compact('students'));
    }

    public function create()
    {
        $students = \App\Models\Student::with(['scores' => function($q) {
            $q->latest();
        }])->active()->paginate(10);
        
        return view('scores.create', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.math_score' => 'nullable|numeric|min:0|max:100',
            'scores.*.khmer_score' => 'nullable|numeric|min:0|max:100',
            'scores.*.science_score' => 'nullable|numeric|min:0|max:100',
            'scores.*.sociology_score' => 'nullable|numeric|min:0|max:100',
            'scores.*.remark' => 'nullable|string',
        ]);

        foreach ($data['scores'] as $scoreData) {
            \App\Models\StudentScore::updateOrCreate(
                [
                    'student_id' => $scoreData['student_id'],
                    // You might add academic_year or term_id here as unique constraints per term
                ],
                [
                    'math_score' => $scoreData['math_score'] ?? null,
                    'khmer_score' => $scoreData['khmer_score'] ?? null,
                    'science_score' => $scoreData['science_score'] ?? null,
                    'sociology_score' => $scoreData['sociology_score'] ?? null,
                    'remark' => $scoreData['remark'] ?? null,
                ]
            );
        }

        return redirect()->route('scores.index')->with('success', 'Scores saved successfully!');
    }
}
