<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function paymentReport(Request $request): JsonResponse
    {
        $query = Payment::with(['student', 'tuitionPlan']);

        if ($request->has('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'by_method' => $payments->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                ];
            }),
        ];

        return response()->json([
            'summary' => $summary,
            'payments' => $payments,
        ]);
    }

    public function studentReport(Request $request): JsonResponse
    {
        $query = Student::with(['classroom.grade', 'term']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->has('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        $students = $query->get();

        $summary = [
            'total_students' => $students->count(),
            'by_status' => $students->groupBy('status')->map->count(),
            'by_gender' => $students->groupBy('gender')->map->count(),
        ];

        return response()->json([
            'summary' => $summary,
            'students' => $students,
        ]);
    }
}
