@extends('layouts.app')
@section('title', 'Student — ' . $student->full_name)
@section('page-title', 'Student Details')

@section('content')
@php
    $currentEnrollment = $student->enrollments->where('is_current', true)->sortByDesc('start_date')->first();
@endphp

<style>
/* Student detail page */
.stu-banner {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
    border-radius: var(--radius-xl);
    padding: 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(99,102,241,0.3);
}
.stu-banner::before {
    content: '';
    position: absolute;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(139,92,246,0.2) 0%, transparent 70%);
    top: -100px; right: -50px;
}
.stu-banner-inner {
    display: flex;
    align-items: center;
    gap: 24px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
}
.stu-avatar {
    width: 80px; height: 80px;
    border-radius: 20px;
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 36px;
    flex-shrink: 0;
    backdrop-filter: blur(8px);
}
.stu-banner-info { flex: 1; }
.stu-banner-info h1 { font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px; letter-spacing: -0.5px; }
.stu-banner-info p { font-size: 13px; color: rgba(255,255,255,0.65); margin-bottom: 12px; }
.stu-banner-chips { display: flex; flex-wrap: wrap; gap: 8px; }
.stu-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
    color: rgba(255,255,255,0.9);
}
.stu-banner-actions { display: flex; gap: 10px; align-self: flex-start; }
.stu-btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: var(--radius-sm);
    color: rgba(255,255,255,0.9);
    font-size: 13px; font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    backdrop-filter: blur(4px);
}
.stu-btn-back:hover { background: rgba(255,255,255,0.22); }

/* Info sections grid */
.stu-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
@media (max-width: 768px) { .stu-grid { grid-template-columns: 1fr; } }
.stu-section-title {
    font-size: 13px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.6px; color: var(--text-muted);
    padding: 16px 20px 10px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; gap: 8px;
}
.stu-kv-table { width: 100%; }
.stu-kv-table tr td { padding: 10px 20px; font-size: 13px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
.stu-kv-table tr:last-child td { border-bottom: none; }
.stu-kv-label { color: var(--text-muted); font-weight: 600; font-size: 12px; width: 130px; white-space: nowrap; }
.stu-kv-value { color: var(--text-primary); font-weight: 500; }

/* Current enrollment highlight */
.enr-current-card {
    background: linear-gradient(135deg, rgba(99,102,241,0.08) 0%, rgba(139,92,246,0.05) 100%);
    border: 1px solid rgba(99,102,241,0.25);
    border-radius: var(--radius-lg);
    margin-bottom: 24px;
    overflow: hidden;
}
.enr-current-header {
    padding: 14px 20px;
    background: rgba(99,102,241,0.1);
    border-bottom: 1px solid rgba(99,102,241,0.2);
    display: flex; align-items: center; justify-content: space-between;
}
.enr-current-header h3 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #818cf8; display: flex; align-items: center; gap: 6px; }
.enr-stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    padding: 16px 20px;
    gap: 16px;
}
.enr-stat-item label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--text-muted); display: block; margin-bottom: 3px; }
.enr-stat-item span { font-size: 14px; font-weight: 700; color: var(--text-primary); }
.enr-stat-item span.accent { color: var(--accent-primary); }

/* Enrollment history table */
.enr-history-table thead th { background: rgba(0,0,0,0.2); }
.enr-badge-current { padding: 2px 8px; background: rgba(99,102,241,0.15); color: #818cf8; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
</style>

{{-- Banner --}}
<div class="stu-banner">
    <div class="stu-banner-inner">
        <div class="stu-avatar">{{ $student->gender === 'female' ? '👩' : '👨' }}</div>
        <div class="stu-banner-info">
            <h1>{{ $student->full_name }}</h1>
            <p>{{ $student->student_code }} · Registered {{ $student->registration_date ? $student->registration_date->format('d M Y') : 'N/A' }}</p>
            <div class="stu-banner-chips">
                <span class="stu-chip">{{ ucfirst($student->gender) }}</span>
                @if($student->date_of_birth)
                    <span class="stu-chip">📅 {{ $student->date_of_birth->format('d M Y') }}</span>
                @endif
                <span class="stu-chip badge {{ $student->status }}" style="border:none;">{{ ucfirst($student->status) }}</span>
                @if($currentEnrollment)
                    <span class="stu-chip">🏫 {{ $currentEnrollment->classroom->name ?? '—' }}</span>
                    <span class="stu-chip">📚 {{ $currentEnrollment->grade->name ?? $currentEnrollment->classroom->grade->name ?? '—' }}</span>
                @endif
            </div>
        </div>
        <div class="stu-banner-actions">
            <a href="{{ route('students.index') }}" class="stu-btn-back">← Back</a>
        </div>
    </div>
</div>

{{-- Current Enrollment Highlight --}}
@if($currentEnrollment)
<div class="enr-current-card">
    <div class="enr-current-header">
        <h3>✅ Current Enrollment</h3>
        <span class="badge active">Active</span>
    </div>
    <div class="enr-stat-row">
        <div class="enr-stat-item">
            <label>Term</label>
            <span>{{ $currentEnrollment->term->name ?? '—' }}</span>
        </div>
        <div class="enr-stat-item">
            <label>Grade</label>
            <span>{{ $currentEnrollment->grade->name ?? $currentEnrollment->classroom->grade->name ?? '—' }}</span>
        </div>
        <div class="enr-stat-item">
            <label>Classroom</label>
            <span class="accent">{{ $currentEnrollment->classroom->name ?? '—' }}</span>
        </div>
        <div class="enr-stat-item">
            <label>Turn</label>
            <span>{{ $currentEnrollment->classroom->turn->name ?? '—' }}</span>
        </div>
        <div class="enr-stat-item">
            <label>Teacher</label>
            <span>{{ $currentEnrollment->classroom->teacher->name ?? '—' }}</span>
        </div>
        <div class="enr-stat-item">
            <label>Start Date</label>
            <span>{{ $currentEnrollment->start_date ? $currentEnrollment->start_date->format('d M Y') : '—' }}</span>
        </div>
    </div>
</div>
@else
<div class="card" style="margin-bottom:24px; padding:20px; text-align:center; color:var(--text-muted);">
    ⚠️ This student has no current enrollment.
</div>
@endif

{{-- Info Grid --}}
<div class="stu-grid">
    {{-- Basic Info --}}
    <div class="card">
        <div class="stu-section-title">👤 Basic Information</div>
        <table class="stu-kv-table">
            <tr><td class="stu-kv-label">Student Code</td><td class="stu-kv-value"><strong>{{ $student->student_code }}</strong></td></tr>
            <tr><td class="stu-kv-label">Gender</td><td class="stu-kv-value">{{ ucfirst($student->gender) }}</td></tr>
            <tr><td class="stu-kv-label">Date of Birth</td><td class="stu-kv-value">{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : '—' }}</td></tr>
            <tr><td class="stu-kv-label">Place of Birth</td><td class="stu-kv-value">{{ $student->place_of_birth ?? '—' }}</td></tr>
            <tr><td class="stu-kv-label">Address</td><td class="stu-kv-value">{{ $student->address ?? '—' }}</td></tr>
            <tr><td class="stu-kv-label">Registered</td><td class="stu-kv-value">{{ $student->registration_date ? $student->registration_date->format('d M Y') : '—' }}</td></tr>
            <tr><td class="stu-kv-label">Status</td><td class="stu-kv-value"><span class="badge {{ $student->status }}">{{ ucfirst($student->status) }}</span></td></tr>
        </table>
    </div>

    {{-- Family Info --}}
    <div class="card">
        <div class="stu-section-title">👨‍👩‍👧 Family Information</div>
        <table class="stu-kv-table">
            <tr><td class="stu-kv-label">Father Name</td><td class="stu-kv-value">{{ $student->father_name ?? '—' }}</td></tr>
            <tr><td class="stu-kv-label">Father Contact</td><td class="stu-kv-value">{{ $student->father_contact ?? '—' }}</td></tr>
            <tr><td class="stu-kv-label">Mother Name</td><td class="stu-kv-value">{{ $student->mother_name ?? '—' }}</td></tr>
            <tr><td class="stu-kv-label">Mother Contact</td><td class="stu-kv-value">{{ $student->mother_contact ?? '—' }}</td></tr>
            <tr><td class="stu-kv-label">Emergency Name</td><td class="stu-kv-value">{{ $student->emergency_name ?? '—' }}</td></tr>
            <tr><td class="stu-kv-label">Emergency Contact</td><td class="stu-kv-value">{{ $student->emergency_contact ?? '—' }}</td></tr>
        </table>
    </div>

    {{-- Health --}}
    <div class="card">
        <div class="stu-section-title">🏥 Health &amp; Characteristics</div>
        <div style="padding:16px 20px;">
            <div style="margin-bottom:14px;">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); margin-bottom:6px;">Health</div>
                <div style="font-size:13px; color:var(--text-primary); line-height:1.6;">{{ $student->health ?: 'None recorded' }}</div>
            </div>
            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); margin-bottom:6px;">Characteristics</div>
                <div style="font-size:13px; color:var(--text-primary); line-height:1.6;">{{ $student->characteristics ?: 'None recorded' }}</div>
            </div>
        </div>
    </div>

    {{-- Payment Summary --}}
    <div class="card">
        <div class="stu-section-title">💳 Payment Summary</div>
        <table class="stu-kv-table">
            <tr><td class="stu-kv-label">Total Payments</td><td class="stu-kv-value"><strong>{{ $student->payments->count() }}</strong></td></tr>
            <tr><td class="stu-kv-label">Total Paid</td><td class="stu-kv-value"><strong style="color:var(--success);">${{ number_format($student->payments->sum('amount'), 2) }}</strong></td></tr>
        </table>
    </div>
</div>

{{-- Enrollment History --}}
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2>📋 Enrollment History</h2>
        <span class="badge info">{{ $student->enrollments->count() }} record(s)</span>
    </div>
    <div class="table-responsive">
        <table class="data-table enr-history-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Term</th>
                    <th>Grade</th>
                    <th>Classroom</th>
                    <th>Turn</th>
                    <th>Teacher</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Enrolled On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($student->enrollments->sortByDesc('start_date') as $i => $enrollment)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $enrollment->term->name ?? '—' }}</td>
                    <td>{{ $enrollment->grade->name ?? $enrollment->classroom->grade->name ?? '—' }}</td>
                    <td><strong>{{ $enrollment->classroom->name ?? '—' }}</strong></td>
                    <td>{{ $enrollment->classroom->turn->name ?? '—' }}</td>
                    <td>{{ $enrollment->classroom->teacher->name ?? '—' }}</td>
                    <td>{{ $enrollment->start_date ? $enrollment->start_date->format('d M Y') : '—' }}</td>
                    <td>{{ $enrollment->end_date ? $enrollment->end_date->format('d M Y') : '—' }}</td>
                    <td>{{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('d M Y') : '—' }}</td>
                    <td>
                        @if($enrollment->is_current)
                            <span class="enr-badge-current">Current</span>
                        @else
                            <span class="badge {{ $enrollment->status }}">{{ ucfirst($enrollment->status ?? 'past') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted" style="padding:30px;">No enrollment records found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Payment History --}}
<div class="card">
    <div class="card-header"><h2>💰 Payment History</h2></div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Plan</th><th>Amount</th><th>Period</th><th>Method</th></tr></thead>
            <tbody>
            @forelse($student->payments as $payment)
                <tr>
                    <td>{{ $payment->tuitionPlan->name ?? 'N/A' }}</td>
                    <td><strong style="color:var(--success);">${{ number_format($payment->amount, 2) }}</strong></td>
                    <td>{{ $payment->start_study_date->format('d/m/Y') }} — {{ $payment->end_study_date->format('d/m/Y') }}</td>
                    <td><span class="badge info">{{ strtoupper($payment->payment_method) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted" style="padding:30px;">No payments recorded.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
