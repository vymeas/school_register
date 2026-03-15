@extends('layouts.app')
@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
    <div class="card">
        <div class="card-header">
            <h2>{{ $student->first_name }} {{ $student->last_name }}</h2>
            <span class="badge {{ $student->status }}">{{ ucfirst($student->status) }}</span>
        </div>
        <div class="card-body">
            <table class="data-table">
                <tr><td class="text-muted" style="width:140px;">Student Code</td><td><strong>{{ $student->student_code }}</strong></td></tr>
                <tr><td class="text-muted">Gender</td><td>{{ ucfirst($student->gender) }}</td></tr>
                <tr><td class="text-muted">Date of Birth</td><td>{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : '—' }}</td></tr>
                <tr><td class="text-muted">Classroom</td><td>{{ $student->classroom->name ?? '—' }}</td></tr>
                <tr><td class="text-muted">Term</td><td>{{ $student->term->name ?? '—' }}</td></tr>
                <tr><td class="text-muted">Parent Name</td><td>{{ $student->parent_name ?? '—' }}</td></tr>
                <tr><td class="text-muted">Parent Phone</td><td>{{ $student->parent_phone ?? '—' }}</td></tr>
                <tr><td class="text-muted">Address</td><td>{{ $student->address ?? '—' }}</td></tr>
                <tr><td class="text-muted">Emergency</td><td>{{ $student->emergency_contact ?? '—' }}</td></tr>
                <tr><td class="text-muted">Registered</td><td>{{ $student->registration_date ? $student->registration_date->format('d M Y') : '—' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Payment History</h2></div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Plan</th><th>Amount</th><th>Period</th><th>Method</th></tr></thead>
                <tbody>
                @forelse($student->payments as $payment)
                    <tr>
                        <td>{{ $payment->tuitionPlan->name ?? 'N/A' }}</td>
                        <td>${{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->start_study_date->format('d/m/Y') }} — {{ $payment->end_study_date->format('d/m/Y') }}</td>
                        <td><span class="badge info">{{ strtoupper($payment->payment_method) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted" style="padding:30px;">No payments</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div style="margin-top:20px;">
    <a href="{{ route('students.index') }}" class="btn btn-secondary">← Back to Students</a>
</div>
@endsection
