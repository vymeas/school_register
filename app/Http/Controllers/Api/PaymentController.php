<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['student', 'tuitionPlan', 'creator']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
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
            'payment' => $payment->load(['student', 'tuitionPlan', 'creator']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $payment = Payment::with(['student', 'tuitionPlan', 'creator', 'logs.user'])->findOrFail($id);

        return response()->json([
            'payment' => $payment,
        ]);
    }
}
