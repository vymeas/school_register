<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Term;
use App\Services\StudentCodeGenerator;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classroom.grade', 'term']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        return view('students.index', [
            'students' => $query->latest()->paginate(15)->withQueryString(),
            'classrooms' => Classroom::orderBy('name')->get(),
            'terms' => Term::orderBy('start_date', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'parent_name' => 'nullable|string',
            'parent_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'term_id' => 'nullable|exists:terms,id',
            'emergency_contact' => 'nullable|string',
        ]);

        $data['student_code'] = StudentCodeGenerator::generate();
        $data['registration_date'] = now()->toDateString();

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['classroom.grade', 'term', 'payments.tuitionPlan', 'enrollments']);
        return view('students.show', compact('student'));
    }
}
