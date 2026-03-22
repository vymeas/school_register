@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="toolbar">
            <select class="form-control" style="width:auto;" id="methodFilter">
                <option value="">All Methods</option>
                <option value="cash" {{ request('method')=='cash'?'selected':'' }}>Cash</option>
                <option value="aba" {{ request('method')=='aba'?'selected':'' }}>ABA</option>
                <option value="acleda" {{ request('method')=='acleda'?'selected':'' }}>ACLEDA</option>
                <option value="wing" {{ request('method')=='wing'?'selected':'' }}>Wing</option>
            </select>
            <select class="form-control" style="width:auto;" id="statusFilter">
                <option value="">All Payment Status</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                <option value="void" {{ request('status')=='void'?'selected':'' }}>Void</option>
            </select>
        </div>
        <button class="btn btn-primary" onclick="openModal('paymentModal')">+ Record Payment</button>
    </div>
    <div class="card-body" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; padding-top: 14px;">
        <div class="stat-card" style="padding: 14px;">
            <div class="stat-label">Active Students</div>
            <div class="stat-value">{{ $activeStudentCount }}</div>
        </div>
        <div class="stat-card" style="padding: 14px;">
            <div class="stat-label">Expired Students</div>
            <div class="stat-value">{{ $expiredStudentCount }}</div>
        </div>
        <div class="stat-card" style="padding: 14px;">
            <div class="stat-label">Expiring in 7 Days</div>
            <div class="stat-value">{{ $expiringSoonCount }}</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Enrollment</th><th>Tuition Plan</th><th>Amount</th><th>Method</th><th>Payment</th><th>Paid Until</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($payments as $payment)
                @php
                    $paidUntil = $payment->end_study_date;
                    $isActive = $paidUntil && now()->lte($paidUntil);
                    $studentStatus = $isActive ? 'active' : 'expired';
                @endphp
                <tr>
                    <td><strong>{{ $payment->student->first_name ?? '' }} {{ $payment->student->last_name ?? '' }}</strong><br><span class="text-muted fs-sm">{{ $payment->student->student_code ?? '' }}</span></td>
                    <td>
                        {{ $payment->enrollment->classroom->name ?? '—' }}
                        <br>
                        <span class="text-muted fs-sm">{{ $payment->enrollment->term->name ?? '—' }}</span>
                    </td>
                    <td>{{ $payment->tuitionPlan->name ?? '—' }}</td>
                    <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                    <td><span class="badge info">{{ strtoupper($payment->payment_method) }}</span></td>
                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                    <td>{{ $payment->end_study_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $studentStatus }}">{{ ucfirst($studentStatus) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">💳</div><h3>No payments</h3><p>Record your first payment.</p><button class="btn btn-primary" onclick="openModal('paymentModal')">+ Record Payment</button></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">
        <div class="pagination-info">Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }}</div>
        <div class="pagination">@for($i=1;$i<=$payments->lastPage();$i++)<a href="{{ $payments->url($i) }}" class="page-btn {{ $payments->currentPage()==$i?'active':'' }}">{{ $i }}</a>@endfor</div>
    </div>
    @endif
</div>

<div class="modal-overlay" id="paymentModal">
    <div class="modal">
        <div class="modal-header"><h3>Record Payment</h3><button class="modal-close" onclick="closeModal('paymentModal')">✕</button></div>
        <div class="modal-body">
            <form id="paymentForm" method="POST" action="{{ route('payments.store') }}">
                @csrf
                <input type="hidden" name="student_id" id="studentIdInput">
                <div class="form-group">
                    <label class="form-label">Enrollment *</label>
                    <select name="enrollment_id" class="form-control" required id="enrollmentSelect">
                        <option value="">Select Enrollment</option>
                        @foreach($enrollments as $enrollment)
                            <option
                                value="{{ $enrollment->id }}"
                                data-student-id="{{ $enrollment->student_id }}"
                            >
                                {{ $enrollment->student->student_code ?? '' }} — {{ $enrollment->student->first_name ?? '' }} {{ $enrollment->student->last_name ?? '' }} | {{ $enrollment->classroom->name ?? '—' }} | {{ $enrollment->term->name ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Tuition Plan *</label><select name="tuition_plan_id" class="form-control" required id="planSelect">@foreach($tuitionPlans as $p)<option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }} — ${{ number_format($p->price,2) }}</option>@endforeach</select></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Amount ($)</label><input type="number" name="amount" id="amountInput" class="form-control" step="0.01" required readonly></div>
                    <div class="form-group"><label class="form-label">Payment Date</label><input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Payment Method *</label><select name="payment_method" class="form-control" required><option value="cash">Cash</option><option value="aba">ABA</option><option value="acleda">ACLEDA</option><option value="wing">Wing</option></select></div>
                    <div class="form-group"><label class="form-label">Reference #</label><input type="text" name="reference_number" class="form-control"></div>
                </div>
                <div class="form-group"><label class="form-label">Note</label><textarea name="note" class="form-control" rows="2"></textarea></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('paymentModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('paymentForm').submit()">Save Payment</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('planSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('amountInput').value = opt.dataset.price || '';
});
document.getElementById('planSelect').dispatchEvent(new Event('change'));

document.getElementById('enrollmentSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('studentIdInput').value = opt.dataset.studentId || '';
});

document.getElementById('methodFilter').addEventListener('change', function() {
    applyFilters();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    applyFilters();
});

function applyFilters() {
    const p = new URLSearchParams(window.location.search);
    const method = document.getElementById('methodFilter').value;
    const status = document.getElementById('statusFilter').value;
    if (method) p.set('method', method);
    else p.delete('method');
    if (status) p.set('status', status);
    else p.delete('status');
    p.delete('page');
    window.location.search = p.toString();
}

if (new URLSearchParams(window.location.search).get('action') === 'create') openModal('paymentModal');
</script>
@endpush
@endsection
