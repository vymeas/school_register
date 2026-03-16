@extends('layouts.app')
@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2>{{ $student->full_name }}</h2>
            <span class="badge {{ $student->status }}">{{ ucfirst($student->status) }}</span>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            {{-- Basic Information --}}
            <div>
                <h3 style="border-bottom: 2px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px;">Basic Information</h3>
                <table class="data-table">
                    <tr><td class="text-muted" style="width: 150px;">Student Code</td><td><strong>{{ $student->student_code }}</strong></td></tr>
                    <tr><td class="text-muted">Gender</td><td>{{ ucfirst($student->gender) }}</td></tr>
                    <tr><td class="text-muted">Date of Birth</td><td>{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : '—' }}</td></tr>
                    <tr><td class="text-muted">Place of Birth</td><td>{{ $student->place_of_birth ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Address</td><td>{{ $student->address ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Registered</td><td>{{ $student->registration_date ? $student->registration_date->format('d M Y') : '—' }}</td></tr>
                </table>
            </div>

            {{-- Education Information --}}
            <div>
                <h3 style="border-bottom: 2px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px;">Education Information</h3>
                <table class="data-table">
                    <tr><td class="text-muted" style="width: 150px;">Study Class</td><td>{{ $student->classroom->grade->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Class Room</td><td>{{ $student->classroom->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Teacher</td><td>{{ $student->classroom->teacher->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Turn</td><td>{{ $student->turn ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Time</td><td>{{ $student->time ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Term</td><td>{{ $student->term->name ?? '—' }}</td></tr>
                </table>
            </div>

            {{-- Family Information --}}
            <div>
                <h3 style="border-bottom: 2px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px;">Family Information</h3>
                <table class="data-table">
                    <tr><td class="text-muted" style="width: 150px;">Father Name</td><td>{{ $student->father_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Father Contact</td><td>{{ $student->father_contact ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Mother Name</td><td>{{ $student->mother_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Mother Contact</td><td>{{ $student->mother_contact ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Emergency Name</td><td>{{ $student->emergency_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Emergency Contact</td><td>{{ $student->emergency_contact ?? '—' }}</td></tr>
                </table>
            </div>

            {{-- Other Information --}}
            <div>
                <h3 style="border-bottom: 2px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px;">Health & Characteristics</h3>
                <div style="margin-bottom: 15px;">
                    <strong class="text-muted">Characteristics:</strong>
                    <p style="margin-top: 5px; line-height: 1.5;">{{ $student->characteristics ?? 'None' }}</p>
                </div>
                <div>
                    <strong class="text-muted">Health:</strong>
                    <p style="margin-top: 5px; line-height: 1.5;">{{ $student->health ?? 'None' }}</p>
                </div>
            </div>
        </div>
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

<div style="margin-top: 20px;">
    <a href="{{ route('students.index') }}" class="btn btn-secondary">← Back to Students</a>
</div>
@endsection
