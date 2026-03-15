<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        return view('enrollments.index', [
            'enrollments' => Enrollment::with(['student', 'classroom.grade', 'term'])->latest('enrollment_date')->paginate(15),
            'students' => Student::orderBy('student_code')->get(),
            'classrooms' => Classroom::orderBy('name')->get(),
            'terms' => Term::orderBy('start_date', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'term_id' => 'required|exists:terms,id',
            'enrollment_date' => 'required|date',
        ]);

        Enrollment::create($data);

        return redirect()->route('enrollments.index')->with('success', 'Enrollment created successfully.');
    }
}
