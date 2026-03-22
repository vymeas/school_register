<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Term;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'term', 'grade', 'classroom.grade', 'classroom.turn']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        return view('enrollments.index', [
            'enrollments' => $query->latest('created_at')->paginate(15)->withQueryString(),
            'students' => Student::orderBy('student_code')->get(),
            'classrooms' => Classroom::with(['grade.term', 'turn'])->orderBy('name')->get(),
            'grades' => Grade::with('term')->orderBy('name')->get(),
            'terms' => Term::orderBy('start_date', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'term_id' => 'required|exists:terms,id',
            'grade_id' => 'required|exists:grades,id',
            'start_date' => 'nullable|date',
        ]);

        EnrollmentService::createEnrollment($data);

        return redirect()->route('enrollments.index')->with('success', 'Enrollment created successfully.');
    }

    public function upgrade(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'grade_id' => 'required|exists:grades,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'start_date' => 'nullable|date',
        ]);

        EnrollmentService::upgradeGrade($enrollment, $data);

        return redirect()->route('enrollments.index')->with('success', 'Enrollment upgraded successfully.');
    }

    public function transfer(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'start_date' => 'nullable|date',
        ]);

        EnrollmentService::transferClassroom($enrollment, $data);

        return redirect()->route('enrollments.index')->with('success', 'Enrollment transferred successfully.');
    }

    public function history(Student $student)
    {
        $history = Enrollment::with(['term', 'grade', 'classroom.grade', 'classroom.turn'])
            ->where('student_id', $student->id)
            ->latest('created_at')
            ->get();

        return response()->json([
            'student' => $student,
            'history' => $history,
        ]);
    }

    public function current(Student $student)
    {
        $current = EnrollmentService::getCurrentEnrollment($student->id);

        return response()->json([
            'student' => $student,
            'current' => $current,
        ]);
    }
}
