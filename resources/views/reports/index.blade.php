@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i data-lucide="graduation-cap"></i></div>
        <div class="stat-value">{{ $totalStudents }}</div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i data-lucide="credit-card" style="width:14px;height:14px;"></i></div>
        <div class="stat-value">{{ $totalPayments }}</div>
        <div class="stat-label">Total Payments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i data-lucide="banknote"></i></div>
        <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

{{-- Payment Report --}}
<div class="card mb-4" style="margin-bottom:20px;">
    <div class="card-header"><h2>Payment Report</h2></div>
    <div class="card-body">
        <form method="GET" class="report-filters">
            <div class="form-group mb-0"><label class="form-label">From</label><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
            <div class="form-group mb-0"><label class="form-label">To</label><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
            <div class="form-group mb-0"><label class="form-label">Method</label><select name="method" class="form-control"><option value="">All</option><option value="cash" {{ request('method')=='cash'?'selected':'' }}>Cash</option><option value="aba" {{ request('method')=='aba'?'selected':'' }}>ABA</option><option value="acleda" {{ request('method')=='acleda'?'selected':'' }}>ACLEDA</option><option value="wing" {{ request('method')=='wing'?'selected':'' }}>Wing</option></select></div>
            <div class="form-group mb-0" style="align-self:flex-end;"><button type="submit" class="btn btn-primary">Filter</button></div>
        </form>

        <div class="report-summary">
            <div class="summary-item"><div class="value">{{ $filteredPayments->count() }}</div><div class="label">Payments</div></div>
            <div class="summary-item"><div class="value">${{ number_format($filteredPayments->sum('amount'), 2) }}</div><div class="label">Total Amount</div></div>
            @foreach($filteredPayments->groupBy('payment_method') as $method => $group)
            <div class="summary-item"><div class="value">${{ number_format($group->sum('amount'), 2) }}</div><div class="label">{{ strtoupper($method) }}</div></div>
            @endforeach
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Plan</th><th>Amount</th><th>Method</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($filteredPayments as $p)
                <tr>
                    <td>{{ $p->student->first_name ?? '' }} {{ $p->student->last_name ?? '' }}</td>
                    <td>{{ $p->tuitionPlan->name ?? '' }}</td>
                    <td>${{ number_format($p->amount, 2) }}</td>
                    <td><span class="badge info">{{ strtoupper($p->payment_method) }}</span></td>
                    <td>{{ $p->payment_date->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted" style="padding:30px;">No payments found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Student Report --}}
<div class="card">
    <div class="card-header"><h2>Student Status Report</h2></div>
    <div class="card-body">
        <div class="report-summary">
            <div class="summary-item"><div class="value text-success">{{ $studentsByStatus['active'] ?? 0 }}</div><div class="label">Active</div></div>
            <div class="summary-item"><div class="value text-warning">{{ $studentsByStatus['pending'] ?? 0 }}</div><div class="label">Pending</div></div>
            <div class="summary-item"><div class="value text-danger">{{ $studentsByStatus['expired'] ?? 0 }}</div><div class="label">Expired</div></div>
        </div>
    </div>
</div>
@endsection
