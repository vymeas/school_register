<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TuitionPlan;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'enrollment.classroom', 'enrollment.term', 'tuitionPlan', 'creator']);

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $today = Carbon::today();
        $sevenDaysLater = Carbon::today()->addDays(7);

        return view('payments.index', [
            'payments' => $query->latest('payment_date')->paginate(15)->withQueryString(),
            'activeStudentCount' => Student::where('status', 'active')->count(),
            'expiredStudentCount' => Student::where('status', 'expired')->count(),
            'expiringSoonCount' => Payment::where('status', 'paid')
                ->whereDate('end_study_date', '>=', $today)
                ->whereDate('end_study_date', '<=', $sevenDaysLater)
                ->distinct('student_id')
                ->count('student_id'),
        ]);
    }

    public function create(Request $request)
    {
        $enrollments = Enrollment::with(['student', 'classroom', 'term'])
            ->where('is_current', true)
            ->whereIn('status', ['pending', 'active'])
            ->latest('start_date')
            ->get();

        $enrollmentsJson = $enrollments->map(function ($e) {
            return [
                'id'           => $e->id,
                'student_id'   => $e->student_id,
                'student_code' => $e->student->student_code ?? '',
                'first_name'   => $e->student->first_name ?? '',
                'last_name'    => $e->student->last_name ?? '',
                'classroom'    => $e->classroom->name ?? '—',
                'term'         => $e->term->name ?? '—',
            ];
        })->values()->toArray();

        return view('payments.create', [
            'enrollmentsJson'   => $enrollmentsJson,
            'tuitionPlans'      => TuitionPlan::where('status', 'active')->orderBy('duration_month')->get(),
            'selectedStudentId' => $request->student_id,
            'selectedPlanId'    => $request->plan_id,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'enrollment_id' => 'required|exists:enrollments,id',
            'tuition_plan_id' => 'required|exists:tuition_plans,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'required|in:cash,aba,acleda,wing',
            'reference_number' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        PaymentService::createPayment($data, (string) Auth::id());

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function showStudentPayments(Student $student)
    {
        $payments = Payment::with(['enrollment.classroom', 'enrollment.term', 'tuitionPlan', 'creator'])
            ->where('student_id', $student->id)
            ->latest('payment_date')
            ->get();

        return response()->json([
            'student' => $student,
            'paid_until' => optional($payments->first())->end_study_date,
            'payments' => $payments,
        ]);
    }
}
