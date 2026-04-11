@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i data-lucide="graduation-cap"></i></div>
        <div class="stat-value">{{ $totalStudents }}</div>
        <div class="stat-label">Total Students</div>
        <div class="stat-change up">↑ Active: {{ $activeStudents }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i data-lucide="user-round-check"></i></div>
        <div class="stat-value">{{ $totalTeachers }}</div>
        <div class="stat-label">Total Teachers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i data-lucide="school"></i></div>
        <div class="stat-value">{{ $totalClassrooms }}</div>
        <div class="stat-label">Classrooms</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i data-lucide="credit-card"></i></div>
        <div class="stat-value">${{ number_format($totalPayments, 0) }}</div>
        <div class="stat-label">Total Payments</div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    {{-- Recent Students --}}
    <div class="card">
        <div class="card-header">
            <h2>Recent Students</h2>
            <a href="{{ route('students.index') }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Code</th><th>Name</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($recentStudents as $student)
                    <tr>
                        <td>{{ $student->student_code }}</td>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td><span class="badge {{ $student->status }}">{{ ucfirst($student->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted" style="padding:30px">No students yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="card">
        <div class="card-header">
            <h2>Recent Payments</h2>
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Student</th><th>Amount</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentPayments as $payment)
                    <tr>
                        <td>{{ $payment->student->first_name ?? 'N/A' }} {{ $payment->student->last_name ?? '' }}</td>
                        <td>${{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted" style="padding:30px">No payments yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
    {{-- Students by Status --}}
    <div class="card">
        <div class="card-header"><h2>Students by Status</h2></div>
        <div class="card-body">
            <div class="report-summary">
                <div class="summary-item">
                    <div class="value text-success">{{ $activeStudents }}</div>
                    <div class="label">Active</div>
                </div>
                <div class="summary-item">
                    <div class="value text-warning">{{ $pendingStudents }}</div>
                    <div class="label">Pending</div>
                </div>
                <div class="summary-item">
                    <div class="value text-danger">{{ $expiredStudents }}</div>
                    <div class="label">Expired</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-header"><h2>Quick Actions</h2></div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('students.index') }}?action=create" class="btn btn-primary" style="justify-content:flex-start;"><i data-lucide="user-plus" style="width:16px;height:16px;"></i> Register New Student</a>
            <a href="{{ route('payments.index') }}?action=create" class="btn btn-secondary" style="justify-content:flex-start;"><i data-lucide="credit-card" style="width:16px;height:16px;"></i> Record Payment</a>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary" style="justify-content:flex-start;"><i data-lucide="bar-chart-3" style="width:16px;height:16px;"></i> View Reports</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>lucide.createIcons();</script>
@endpush
