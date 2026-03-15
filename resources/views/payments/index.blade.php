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
        </div>
        <button class="btn btn-primary" onclick="openModal('paymentModal')">+ Record Payment</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Tuition Plan</th><th>Amount</th><th>Method</th><th>Study Period</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td><strong>{{ $payment->student->first_name ?? '' }} {{ $payment->student->last_name ?? '' }}</strong><br><span class="text-muted fs-sm">{{ $payment->student->student_code ?? '' }}</span></td>
                    <td>{{ $payment->tuitionPlan->name ?? '—' }}</td>
                    <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                    <td><span class="badge info">{{ strtoupper($payment->payment_method) }}</span></td>
                    <td>{{ $payment->start_study_date->format('d/m/Y') }} — {{ $payment->end_study_date->format('d/m/Y') }}</td>
                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">💳</div><h3>No payments</h3><p>Record your first payment.</p><button class="btn btn-primary" onclick="openModal('paymentModal')">+ Record Payment</button></div></td></tr>
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
                <div class="form-group"><label class="form-label">Student *</label><select name="student_id" class="form-control" required><option value="">Select Student</option>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->student_code }} — {{ $s->first_name }} {{ $s->last_name }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label">Tuition Plan *</label><select name="tuition_plan_id" class="form-control" required id="planSelect">@foreach($tuitionPlans as $p)<option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }} — ${{ number_format($p->price,2) }}</option>@endforeach</select></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Amount ($)</label><input type="number" name="amount" id="amountInput" class="form-control" step="0.01"></div>
                    <div class="form-group"><label class="form-label">Start Study Date *</label><input type="date" name="start_study_date" class="form-control" required value="{{ date('Y-m-d') }}"></div>
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
document.getElementById('methodFilter').addEventListener('change', function() {
    const p = new URLSearchParams(window.location.search);
    if (this.value) p.set('method', this.value);
    else p.delete('method');
    window.location.search = p.toString();
});
if (new URLSearchParams(window.location.search).get('action') === 'create') openModal('paymentModal');
</script>
@endpush
@endsection
