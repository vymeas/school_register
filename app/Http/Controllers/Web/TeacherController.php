<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with('classroom.grade');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return view('teachers.index', [
            'teachers' => $query->orderBy('name')->paginate(15)->withQueryString(),
            'classrooms' => Classroom::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
        ]);

        Teacher::create($data);

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }
}
