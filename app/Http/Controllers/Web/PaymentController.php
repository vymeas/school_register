<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TuitionPlan;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $latestPaymentIds = Payment::selectRaw('MAX(id) as id')
            ->groupBy('student_id')
            ->pluck('id');

        $query = Payment::whereIn('id', $latestPaymentIds)
            ->with(['student.payments.tuitionPlan', 'tuitionPlan', 'creator']);

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        return view('payments.index', [
            'payments' => $query->latest()->paginate(15)->withQueryString(),
            'students' => Student::orderBy('student_code')->get(),
            'tuitionPlans' => TuitionPlan::where('status', 'active')->orderBy('duration_month')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'tuition_plan_id' => 'required|exists:tuition_plans,id',
            'amount' => 'nullable|numeric|min:0',
            'start_study_date' => 'required|date',
            'payment_method' => 'required|in:cash,aba,acleda,wing',
            'reference_number' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        PaymentService::createPayment($data, auth()->id());

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }
}
