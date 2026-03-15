<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'tuitionPlan']);

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        return view('reports.index', [
            'totalStudents' => Student::count(),
            'totalPayments' => Payment::count(),
            'totalRevenue' => Payment::sum('amount'),
            'filteredPayments' => $query->latest('payment_date')->get(),
            'studentsByStatus' => Student::selectRaw('status, count(*) as count')
                ->groupBy('status')->pluck('count', 'status')->toArray(),
        ]);
    }
}
