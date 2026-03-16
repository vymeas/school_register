<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('teacher_code', 'like', "%{$search}%");
            });
        }

        return view('teachers.index', [
            'teachers' => $query->orderBy('name')->paginate(15)->withQueryString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_code' => 'nullable|string|unique:teachers,teacher_code',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        // Auto-generate teacher_code if not provided
        if (empty($data['teacher_code'])) {
            $data['teacher_code'] = 'TCH-' . strtoupper(substr(uniqid(), -6));
        }

        Teacher::create($data);

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'teacher_code' => 'nullable|string|unique:teachers,teacher_code,' . $teacher->id,
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $teacher->update($data);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function restoreIndex()
    {
        return view('settings.teachers-restore', [
            'teachers' => Teacher::where('is_delete', true)->orderBy('name')->get(),
        ]);
    }
}
