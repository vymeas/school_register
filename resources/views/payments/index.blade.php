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
        <button class="btn btn-primary" onclick="openPaymentModal()">+ Record Payment</button>
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
                    <td><span class="badge secondary">{{ $payment->tuitionPlan->classroom ?? '—' }}</span></td>
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

<div class="modal-overlay" id="viewPaymentModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Payment History</h3>
            <button class="modal-close" onclick="closeModal('viewPaymentModal')">✕</button>
        </div>
        <div class="modal-body" id="paymentDetails" style="padding: 20px;">
            <div class="modal-toolbar" style="display: flex; gap: 10px; margin-bottom: 15px; background: #f8f9fa; padding: 10px; border-radius: 8px; flex-wrap: nowrap; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 5px; flex: 1;">
                    <label class="fs-sm" style="white-space: nowrap;">From:</label>
                    <input type="date" id="historyFrom" class="form-control" style="width: 100%;" onchange="applyHistoryFilters()">
                </div>
                <div style="display: flex; align-items: center; gap: 5px; flex: 1;">
                    <label class="fs-sm" style="white-space: nowrap;">To:</label>
                    <input type="date" id="historyTo" class="form-control" style="width: 100%;" onchange="applyHistoryFilters()">
                </div>
                <div style="flex: 1;">
                    <select id="historyClassroom" class="form-control" style="width: 100%;" onchange="applyHistoryFilters()">
                        <option value="">All Classrooms</option>
                    </select>
                </div>
            </div>
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Study Period</th>
                        <th>Classroom</th>
                        <th>Tuition Plan</th>
                        <th>Method</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="paymentHistoryBody">
                    <!-- History will be populated by JS -->
                </tbody>
            </table>
        </div>
        <div class="modal-footer" style="display: flex; justify-content: space-between;">
            <button class="btn btn-secondary" onclick="closeModal('viewPaymentModal')">Close</button>
            <button class="btn btn-primary" onclick="printHistory()">Print History</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="receiptVoucherModal">
    <div class="modal" style="max-width: 900px; width: 95%;">
        <div class="modal-header">
            <h3>Receipt Voucher (វិក្កយបត្រ)</h3>
            <button class="modal-close" onclick="closeModal('receiptVoucherModal')">✕</button>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <div id="voucherStudentHeader" class="info-grid" style="grid-template-columns: 1fr 1fr 1fr; border: 1px solid #ddd; margin-bottom: 20px;">
                <!-- Populated by JS -->
            </div>
            
            <table class="item-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="border: 1px solid #ddd; padding: 5px; width: 40px;">ល.រ<br>(No)</th>
                        <th style="border: 1px solid #ddd; padding: 5px;">បរិយាយ<br>(Description)</th>
                        <th style="border: 1px solid #ddd; padding: 5px; width: 60px;">បរិមាណ<br>(Qty)</th>
                        <th style="border: 1px solid #ddd; padding: 5px; width: 100px;">តម្លៃ ($)<br>(Price)</th>
                        <th style="border: 1px solid #ddd; padding: 5px; width: 80px;">បញ្ចុះ (%)<br>(Disc)</th>
                        <th style="border: 1px solid #ddd; padding: 5px; width: 100px;">សរុប ($)<br>(Total $)</th>
                        <th style="border: 1px solid #ddd; padding: 5px; width: 120px;">សរុប (រ)<br>(Total R)</th>
                        <th style="border: 1px solid #ddd; padding: 5px; width: 40px;">លុប</th>
                    </tr>
                </thead>
                <tbody id="voucherItemsBody">
                    <!-- Rows added by JS -->
                </tbody>
                <tfoot>
                    <tr style="background: #eee; font-weight: bold;">
                        <td colspan="5" style="border: 1px solid #ddd; padding: 8px; text-align: right;">សរុប (Total)</td>
                        <td id="voucherGrandTotalUsd" style="border: 1px solid #ddd; padding: 8px; text-align: center;">0.00</td>
                        <td id="voucherGrandTotalRiel" style="border: 1px solid #ddd; padding: 8px; text-align: center;">0</td>
                        <td style="border: 1px solid #ddd;"></td>
                    </tr>
                </tfoot>
            </table>
            <button class="btn btn-sm btn-info" style="margin-top: 10px;" onclick="addVoucherRow()">+ Add Item</button>
        </div>
        <div class="modal-footer" style="display: flex; justify-content: space-between;">
            <button class="btn btn-secondary" onclick="closeModal('receiptVoucherModal')">Cancel</button>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-success" onclick="saveVoucher()">Save</button>
                <button class="btn btn-primary" onclick="printVoucherFinal()">Print</button>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
.info-grid { display: grid; border: 1.5px solid #000; margin-bottom: 15px; grid-template-columns: 1fr 1fr 1fr; }
.info-item { border: 0.5px solid #000; padding: 5px; position: relative; min-height: 45px; display: flex; flex-direction: column; }
.info-item input { width: 100%; border: none; background: transparent; padding: 2px; font-family: inherit; }
.v-label-text { font-size: 11px; color: #333; font-weight: normal; margin-bottom: 2px; }
.v-value-input { font-weight: bold; font-size: 14px; border-bottom: 1px dotted #888 !important; margin-top: auto; }
.item-table input { width: 100%; border: none; background: transparent; padding: 5px; font-family: inherit; font-size: 13px; text-align: center; }
.item-table input:focus, .info-item input:focus { outline: 1px solid #3b82f6; background: #fff; }
.item-table tr:hover { background: #fcfcfc; }
</style>
@endpush

@push('scripts')
<script>
document.getElementById('planSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('amountInput').value = opt.dataset.price || '';
});

let currentHistory = [];
let currentStudent = null;

function viewPayment(paymentData) {
    const p = typeof paymentData === 'string' ? JSON.parse(paymentData) : paymentData;
    currentStudent = p.student;
    currentHistory = p.student.payments || [p];

    // Populate Classroom Filter
    const classroomSelect = document.getElementById('historyClassroom');
    classroomSelect.innerHTML = '<option value="">All Classrooms</option>';
    const classrooms = [...new Set(currentHistory.map(pay => pay.tuition_plan ? pay.tuition_plan.classroom : null).filter(c => c))];
    classrooms.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        classroomSelect.appendChild(opt);
    });

    // Reset Date Filters
    document.getElementById('historyFrom').value = '';
    document.getElementById('historyTo').value = '';

    renderHistoryTable(currentHistory);
    openModal('viewPaymentModal');
}

function renderHistoryTable(payments) {
    const historyBody = document.getElementById('paymentHistoryBody');
    historyBody.innerHTML = '';

    payments.forEach(pay => {
        const row = document.createElement('tr');
        const startDate = new Date(pay.start_study_date).toLocaleDateString();
        const endDate = new Date(pay.end_study_date).toLocaleDateString();
        const planName = pay.tuition_plan ? pay.tuition_plan.name : '—';
        const classroom = pay.tuition_plan ? (pay.tuition_plan.classroom || '—') : '—';
        const method = pay.payment_method ? pay.payment_method.toUpperCase() : '—';
        const amount = parseFloat(pay.amount).toFixed(2);

        row.innerHTML = `
            <td>${startDate} — ${endDate}</td>
            <td><span class="badge secondary">${classroom}</span></td>
            <td>${planName}</td>
            <td><span class="badge info">${method}</span></td>
            <td><strong>$${amount}</strong></td>
        `;
        historyBody.appendChild(row);
    });
}

function applyHistoryFilters() {
    const from = document.getElementById('historyFrom').value;
    const to = document.getElementById('historyTo').value;
    const classroom = document.getElementById('historyClassroom').value;

    const filtered = currentHistory.filter(pay => {
        const payDate = pay.start_study_date;
        const matchesDate = (!from || payDate >= from) && (!to || payDate <= to);
        const matchesClass = !classroom || (pay.tuition_plan && pay.tuition_plan.classroom === classroom);
        return matchesDate && matchesClass;
    });

    renderHistoryTable(filtered);
}

function printHistory() {
    const from = document.getElementById('historyFrom').value;
    const to = document.getElementById('historyTo').value;
    const classroom = document.getElementById('historyClassroom').value;
    
    const filtered = currentHistory.filter(pay => {
        const payDate = pay.start_study_date;
        const matchesDate = (!from || payDate >= from) && (!to || payDate <= to);
        const matchesClass = !classroom || (pay.tuition_plan && pay.tuition_plan.classroom === classroom);
        return matchesDate && matchesClass;
    });

    const printWindow = window.open('', '_blank');
    let rowsHtml = filtered.map(pay => `
        <tr>
            <td>${new Date(pay.start_study_date).toLocaleDateString()} — ${new Date(pay.end_study_date).toLocaleDateString()}</td>
            <td>${pay.tuition_plan ? pay.tuition_plan.classroom : '—'}</td>
            <td>${pay.tuition_plan ? pay.tuition_plan.name : '—'}</td>
            <td>${pay.payment_method.toUpperCase()}</td>
            <td>$${parseFloat(pay.amount).toFixed(2)}</td>
        </tr>
    `).join('');

    const content = `
        <html>
        <head>
            <title>Payment History - ${currentStudent.student_code}</title>
            <style>
                body { font-family: sans-serif; padding: 20px; }
                h1 { text-align: center; border-bottom: 2px solid #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                th { background: #f2f2f2; }
            </style>
        </head>
        <body onload="window.print()">
            <h1>Payment History: ${currentStudent.first_name} ${currentStudent.last_name} (${currentStudent.student_code})</h1>
            <table>
                <thead>
                    <tr>
                        <th>Study Period</th>
                        <th>Classroom</th>
                        <th>Tuition Plan</th>
                        <th>Method</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>${rowsHtml}</tbody>
            </table>
        </body>
        </html>
    `;
    printWindow.document.write(content);
    printWindow.document.close();
}

function openPaymentModal() {
    document.getElementById('paymentForm').reset();
    document.getElementById('studentIdInput').value = '';
    document.getElementById('planSelect').dispatchEvent(new Event('change'));
    openModal('paymentModal');
}

function repayStudent(studentId, planId) {
    const enrollmentSelect = document.getElementById('enrollmentSelect');
    
    // Find first enrollment for this student
    for (let opt of enrollmentSelect.options) {
        if (opt.dataset.studentId === String(studentId)) {
            enrollmentSelect.value = opt.value;
            break;
        }
    }
    
    document.getElementById('studentIdInput').value = studentId;

    const planSelect = document.getElementById('planSelect');
    planSelect.value = planId;
    planSelect.dispatchEvent(new Event('change'));
    openModal('paymentModal');
}
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
