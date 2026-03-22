<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\TuitionPlan;
use App\Models\Turn;
use App\Services\StudentCodeGenerator;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with([
            'classroom.grade',
            'classroom.teacher',
            'classroom.turn',
            'term',
            'latestPayment',
            'enrollments' => fn($q) => $q->where('is_current', true)
                ->with(['classroom.grade', 'classroom.teacher', 'classroom.turn', 'term', 'grade'])
                ->latest('start_date')
                ->limit(1),
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('study_status')) {
            $query->where('study_status', $request->study_status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('term_id')) {
            $query->whereHas('classroom.grade', function ($q) use ($request) {
                $q->where('term_id', $request->term_id);
            });
        }

        if ($request->filled('turn_id')) {
            $query->whereHas('classroom', function ($q) use ($request) {
                $q->where('turn_id', $request->turn_id);
            });
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        $allowedSorts = [
            'first_name', 'last_name', 'gender', 'date_of_birth', 'place_of_birth',
            'father_name', 'mother_name', 'address', 'status', 'registration_date',
            'student_code', 'turn', 'time', 'characteristics', 'health', 
            'emergency_name', 'teacher_name', 'classroom_name', 'grade_name',
            'father_contact', 'mother_contact', 'emergency_contact', 'start_date'
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
            'students'     => $query->paginate(15)->withQueryString(),
            'classrooms'   => Classroom::with(['grade', 'turn'])->orderBy('name')->get(),
            'terms'        => Term::orderBy('start_date', 'desc')->get(),
            'turns'        => Turn::orderBy('start_time')->get(),
            'tuitionPlans' => TuitionPlan::where('status', 'active')->orderBy('duration_month')->get(),
            'enrollments'  => Enrollment::with(['student', 'classroom', 'term'])
                ->where('is_current', true)
                ->whereIn('status', ['pending', 'active'])
                ->latest('start_date')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_code' => 'nullable|string|unique:students,student_code|max:50',
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
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_date' => 'nullable|date',
        ]);

        if (empty($data['student_code'])) {
            $data['student_code'] = StudentCodeGenerator::generate();
        }
        $data['registration_date'] = now()->toDateString();

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load([
            'classroom.grade',
            'classroom.teacher',
            'classroom.turn',
            'term',
            'payments.tuitionPlan',
            'enrollments.classroom.grade',
            'enrollments.classroom.teacher',
            'enrollments.classroom.turn',
            'enrollments.term',
            'enrollments.grade',
        ]);
        return view('students.show', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'student_code' => 'required|string|max:50|unique:students,student_code,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'father_contact' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_contact' => 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'health'        => 'nullable|string',
            'status'        => 'sometimes|in:active,pending,expired',
            'study_status'  => 'sometimes|in:studying,dropped',
        ]);

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function updateStudyStatus(Request $request, Student $student)
    {
        $request->validate([
            'study_status' => 'required|in:studying,dropped',
        ]);

        $student->update(['study_status' => $request->study_status]);

        return redirect()->route('students.index')
            ->with('success', "Study status updated to \"" . ucfirst($request->study_status) . "\" for {$student->full_name}.");
    }

    public function restoreIndex()
    {
        return view('settings.students-restore', [
            'students' => Student::onlyDeleted()->orderBy('first_name')->get(),
        ]);
    }
}
