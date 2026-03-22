<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\Turn;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::with(['grade.term', 'teacher', 'turn'])
            ->withCount([
                'enrollments as enrollment_students_count' => function ($q) {
                    $q->whereColumn('enrollments.classroom_id', 'classrooms.id')
                      ->whereColumn('enrollments.grade_id', 'classrooms.grade_id')
                      ->whereRaw('enrollments.term_id = (SELECT term_id FROM grades WHERE grades.id = classrooms.grade_id LIMIT 1)');
                },
            ]);

        if ($request->filled('term_id')) {
            $query->whereHas('grade', function ($q) use ($request) {
                $q->where('term_id', $request->term_id);
            });
        }

        if ($request->filled('turn_id')) {
            $query->where('turn_id', $request->turn_id);
        }

        return view('classrooms.index', [
            'classrooms' => $query->orderBy('name')->get(),
            'grades' => Grade::with('term')->orderBy('name')->get(),
            'terms' => Term::orderBy('name')->get(),
            'turns' => Turn::orderBy('start_time')->get(),
            'teachers' => Teacher::where('is_delete', false)->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'turn_id' => 'required|exists:turns,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        if ($request->filled('teacher_id')) {
            $error = $this->validateTeacherAssignment($request->teacher_id, $request->grade_id, $request->turn_id);
            if ($error) {
                return back()->withErrors(['teacher_id' => $error])->withInput();
            }
        }

        Classroom::create($data);

        return redirect()->route('classrooms.index')->with('success', 'Classroom created successfully.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'turn_id' => 'required|exists:turns,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        if ($request->filled('teacher_id')) {
            $error = $this->validateTeacherAssignment($request->teacher_id, $request->grade_id, $request->turn_id, $classroom->id);
            if ($error) {
                return back()->withErrors(['teacher_id' => $error])->withInput();
            }
        }

        $classroom->update($data);

        return redirect()->route('classrooms.index')->with('success', 'Classroom updated successfully.');
    }

    private function validateTeacherAssignment($teacherId, $gradeId, $turnId, $classroomId = null)
    {
        $grade = Grade::findOrFail($gradeId);
        $termId = $grade->term_id;

        $existingAssignments = Classroom::where('teacher_id', $teacherId)
            ->whereHas('grade', function ($query) use ($termId) {
                $query->where('term_id', $termId);
            })
            ->when($classroomId, function ($query) use ($classroomId) {
                $query->where('id', '!=', $classroomId);
            })
            ->get();

        if ($existingAssignments->count() >= 2) {
            return 'This teacher is already assigned to 2 classrooms in this term.';
        }

        if ($existingAssignments->contains('turn_id', $turnId)) {
            return 'This teacher is already assigned to another classroom in the same term and turn.';
        }

        return null;
    }

    public function archiveIndex()
    {
        $classrooms = Classroom::withoutGlobalScope('active')
            ->with(['grade.term', 'teacher', 'turn'])
            ->where('is_archived', true)
            ->orderBy('name')
            ->get();

        return view('classrooms.archived', compact('classrooms'));
    }

    public function restore(int $id)
    {
        $classroom = Classroom::withoutGlobalScope('active')->findOrFail($id);
        $classroom->update(['is_archived' => false]);

        return redirect()->route('classrooms.archived')->with('success', "Classroom \"{$classroom->name}\" restored successfully.");
    }
}
