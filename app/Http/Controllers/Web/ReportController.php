<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Enrollment;
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
            $query->where('payment_method', $request->input('method'));
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
    public function classroom(Request $request)
    {
        $terms  = \App\Models\Term::orderByDesc('id')->get();
        $grades = $request->filled('term_id')
            ? \App\Models\Grade::where('term_id', $request->term_id)->where('is_delete', false)->orderBy('name')->get()
            : collect();

        $classrooms = Classroom::with([
            'enrollments.student',
            'enrollments.payments',
            'grade.term',
            'turn',
            'teacher',
        ])
        ->when($request->filled('term_id'),  fn($q) => $q->whereHas('grade', fn($g) => $g->where('term_id',  $request->term_id)))
        ->when($request->filled('grade_id'), fn($q) => $q->where('grade_id', $request->grade_id))
        ->orderBy('name')
        ->get();

        return view('reports.classroom', [
            'classrooms' => $classrooms,
            'terms'      => $terms,
            'grades'     => $grades,
            'selTermId'  => $request->term_id,
            'selGradeId' => $request->grade_id,
        ]);
    }
    public function classroomSummary()
    {
        // Include archived - use withoutGlobalScope
        $classrooms = Classroom::withoutGlobalScope('active')
            ->with(['enrollments', 'grade.term', 'teacher', 'turn'])
            ->orderBy('is_delete')
            ->orderBy('name')
            ->get();

        return view('reports.classroom_summary', [
            'classrooms'    => $classrooms,
            'totalActive'   => $classrooms->where('is_delete', false)->count(),
            'totalArchived' => $classrooms->where('is_delete', true)->count(),
        ]);
    }

    public function classroomUnpaid()
    {
        $classrooms = Classroom::with([
            'enrollments.student',
            'enrollments.payments',
            'grade.term',
            'teacher',
            'turn',
        ])
        ->whereHas('enrollments', fn($q) => $q->where('is_current', true)->whereIn('status', ['active', 'pending']))
        ->orderBy('name')
        ->get();

        // For each classroom, only keep enrollments where student has no valid payment
        $classrooms = $classrooms->map(function ($classroom) {
            $classroom->unpaidEnrollments = $classroom->enrollments
                ->where('is_current', true)
                ->whereIn('status', ['active', 'pending'])
                ->filter(function ($enrollment) {
                    $lastPay   = $enrollment->payments->sortByDesc('payment_date')->first();
                    $paidUntil = $lastPay?->end_study_date;
                    return !$paidUntil || now()->gt($paidUntil);
                });
            return $classroom;
        })->filter(fn($c) => $c->unpaidEnrollments->isNotEmpty());

        return view('reports.classroom_unpaid', [
            'classrooms'       => $classrooms,
            'totalUnpaidStudents' => $classrooms->sum(fn($c) => $c->unpaidEnrollments->count()),
        ]);
    }

    // ══ STUDENT REPORTS ══════════════════════
    public function studentSummary(Request $request)
    {
        $students = \App\Models\Student::with(['enrollments' => fn($q) => $q->where('is_current', true)->with(['classroom', 'grade', 'payments'])])
            ->where('is_delete', false)
            ->when($request->filled('study_status'), fn($q) => $q->where('study_status', $request->study_status))
            ->orderBy('first_name')->get();

        $byGrade  = $students->flatMap->enrollments->groupBy(fn($e) => $e->grade->name ?? 'Unknown');
        $byStatus = $students->groupBy('study_status');

        return view('reports.students', compact('students', 'byGrade', 'byStatus'));
    }

    public function studentProfile(\App\Models\Student $student)
    {
        $student->load(['enrollments.classroom', 'enrollments.grade.term', 'enrollments.payments.tuitionPlan']);
        return view('reports.student_profile', compact('student'));
    }

    // ══ TERM/GRADE REPORTS ════════════════════
    public function termGradeSummary()
    {
        $terms = \App\Models\Term::with(['grades.classrooms.enrollments', 'grades.classrooms.teacher'])
            ->orderByDesc('id')->get();
        return view('reports.term_grade', compact('terms'));
    }

    public function termGradeDetail(\App\Models\Grade $grade)
    {
        $grade->load(['term', 'classrooms.teacher', 'classrooms.turn', 'classrooms.enrollments.student', 'classrooms.enrollments.payments']);
        return view('reports.term_grade_detail', compact('grade'));
    }

    // ══ TEACHER REPORTS ══════════════════════
    public function teacherSummary()
    {
        $teachers = \App\Models\Teacher::withCount('classrooms')
            ->with(['classrooms.grade.term', 'classrooms.turn'])
            ->where('is_delete', false)->orderBy('name')->get();
        return view('reports.teachers', compact('teachers'));
    }

    public function teacherSchedule(\App\Models\Teacher $teacher)
    {
        $teacher->load(['classrooms.grade.term', 'classrooms.turn', 'classrooms.enrollments']);
        return view('reports.teacher_schedule', compact('teacher'));
    }

    // ══ PAYMENT REPORTS ═══════════════════════
    public function paymentRevenue()
    {
        $today    = \Carbon\Carbon::today();
        $todayRev = Payment::whereDate('payment_date', $today)->sum('amount');
        $weekRev  = Payment::whereBetween('payment_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])->sum('amount');
        $monthRev = Payment::whereYear('payment_date', $today->year)->whereMonth('payment_date', $today->month)->sum('amount');
        $yearRev  = Payment::whereYear('payment_date', $today->year)->sum('amount');

        $byPlan   = Payment::with('tuitionPlan')->selectRaw('tuition_plan_id, sum(amount) as total, count(*) as cnt')->groupBy('tuition_plan_id')->get();
        $byMethod = Payment::selectRaw('payment_method, sum(amount) as total, count(*) as cnt')->groupBy('payment_method')->get();
        $monthly  = Payment::selectRaw('MONTH(payment_date) as m, YEAR(payment_date) as y, sum(amount) as total')
            ->whereYear('payment_date', $today->year)
            ->groupByRaw('YEAR(payment_date), MONTH(payment_date)')
            ->orderByRaw('YEAR(payment_date), MONTH(payment_date)')->get();

        return view('reports.payment_revenue', compact('todayRev', 'weekRev', 'monthRev', 'yearRev', 'byPlan', 'byMethod', 'monthly', 'today'));
    }

    public function paymentTransactions(Request $request)
    {
        $query = Payment::with(['student', 'enrollment.classroom', 'tuitionPlan', 'creator'])
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('payment_date', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('payment_date', '<=', $request->date_to))
            ->when($request->filled('method'),    fn($q) => $q->where('payment_method', $request->input('method')))
            ->latest('payment_date');

        $total    = (clone $query)->sum('amount');
        $payments = $query->paginate(50)->withQueryString();

        return view('reports.payment_transactions', compact('payments', 'total'));
    }

    public function paymentOverdue()
    {
        $today = \Carbon\Carbon::today();

        $enrollments = \App\Models\Enrollment::with(['student', 'classroom', 'grade', 'payments' => fn($q) => $q->latest('payment_date')])
            ->where('is_current', true)->whereIn('status', ['active','pending'])->get()
            ->filter(function ($e) use ($today) {
                $until = $e->payments->first()?->end_study_date;
                return !$until || $today->gt($until);
            })
            ->map(function ($e) use ($today) {
                $until = $e->payments->first()?->end_study_date;
                $e->daysOverdue  = $until ? \Carbon\Carbon::parse($until)->diffInDays($today) : null;
                $e->lastPayment  = $e->payments->first();
                return $e;
            })->sortByDesc('daysOverdue');

        return view('reports.payment_overdue', compact('enrollments', 'today'));
    }

    public function paymentReceipt(Payment $payment)
    {
        $payment->load(['student', 'enrollment.classroom', 'enrollment.grade.term', 'tuitionPlan', 'creator']);
        return view('reports.payment_receipt', compact('payment'));
    }
}
