<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalStudents' => Student::count(),
            'activeStudents' => Student::where('status', 'active')->count(),
            'pendingStudents' => Student::where('status', 'pending')->count(),
            'expiredStudents' => Student::where('status', 'expired')->count(),
            'totalTeachers' => Teacher::count(),
            'totalClassrooms' => Classroom::count(),
            'totalPayments' => Payment::sum('amount'),
            'recentStudents' => Student::latest()->take(5)->get(),
            'recentPayments' => Payment::with('student')->latest()->take(5)->get(),
        ]);
    }
}
