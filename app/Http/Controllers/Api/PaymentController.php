<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['student', 'enrollment.classroom', 'enrollment.term', 'tuitionPlan', 'creator']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->has('enrollment_id')) {
            $query->where('enrollment_id', $request->enrollment_id);
        }
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json($payments);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = PaymentService::createPayment(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'message' => 'Payment created successfully.',
            'payment' => $payment->load(['student', 'enrollment.classroom', 'enrollment.term', 'tuitionPlan', 'creator']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $payment = Payment::with(['student', 'enrollment.classroom', 'enrollment.term', 'tuitionPlan', 'creator', 'logs.user'])->findOrFail($id);

        return response()->json([
            'payment' => $payment,
        ]);
    }

    public function showStudentPayments(Student $student): JsonResponse
    {
        $payments = Payment::with(['enrollment.classroom', 'enrollment.term', 'tuitionPlan', 'creator'])
            ->where('student_id', $student->id)
            ->orderByDesc('payment_date')
            ->get();

        return response()->json([
            'student' => $student,
            'paid_until' => optional($payments->first())->end_study_date,
            'payments' => $payments,
        ]);
    }
}
