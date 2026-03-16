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
        $query = Student::with(['classroom.grade', 'classroom.teacher', 'term']);

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

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        $allowedSorts = [
            'first_name', 'last_name', 'gender', 'date_of_birth', 'place_of_birth',
            'father_name', 'mother_name', 'address', 'status', 'registration_date',
            'student_code', 'turn', 'time', 'characteristics', 'health', 
            'emergency_name', 'teacher_name', 'classroom_name', 'grade_name',
            'father_contact', 'mother_contact', 'emergency_contact'
        ];

        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'teacher_name') {
                $query->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
                    ->join('teachers', 'classrooms.teacher_id', '=', 'teachers.id')
                    ->orderBy('teachers.name', $direction)
                    ->select('students.*');
            } elseif ($sort === 'classroom_name') {
                $query->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
                    ->orderBy('classrooms.name', $direction)
                    ->select('students.*');
            } elseif ($sort === 'grade_name') {
                $query->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
                    ->join('grades', 'classrooms.grade_id', '=', 'grades.id')
                    ->orderBy('grades.name', $direction)
                    ->select('students.*');
            } else {
                $query->orderBy($sort, $direction);
            }
        } else {
            $query->latest();
        }

        return view('students.index', [
            'students' => $query->paginate(15)->withQueryString(),
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
            'place_of_birth' => 'nullable|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'father_contact' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'characteristics' => 'nullable|string',
            'health' => 'nullable|string',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'term_id' => 'nullable|exists:terms,id',
            'turn' => 'nullable|string|max:255',
            'time' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_name' => 'nullable|string|max:255',
        ]);

        $data['student_code'] = StudentCodeGenerator::generate();
        $data['registration_date'] = now()->toDateString();

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['classroom.grade', 'classroom.teacher', 'term', 'payments.tuitionPlan', 'enrollments']);
        return view('students.show', compact('student'));
    }
}
