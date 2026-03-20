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
        <button class="btn btn-primary" onclick="openPaymentModal()">+ Record Payment</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Tuition Plan</th><th>Classroom</th><th>Amount</th><th>Method</th><th>Study Period</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td><strong>{{ $payment->student->first_name ?? '' }} {{ $payment->student->last_name ?? '' }}</strong><br><span class="text-muted fs-sm">{{ $payment->student->student_code ?? '' }}</span></td>
                    <td>{{ $payment->tuitionPlan->name ?? '—' }}</td>
                    <td><span class="badge secondary">{{ $payment->tuitionPlan->classroom ?? '—' }}</span></td>
                    <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                    <td><span class="badge info">{{ strtoupper($payment->payment_method) }}</span></td>
                    <td>{{ $payment->start_study_date->format('d/m/Y') }} — {{ $payment->end_study_date->format('d/m/Y') }}</td>
                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-secondary" title="View" data-payment="{{ $payment->toJson() }}" onclick="viewPayment(this.dataset.payment)">👁️</button>
                            <button class="btn btn-sm btn-secondary" title="Repay" onclick="repayStudent({{ $payment->student_id }}, {{ $payment->tuition_plan_id }})">🔄</button>
                            <button class="btn btn-sm btn-secondary" title="Receipt Voucher" onclick="openReceiptVoucher(this.dataset.payment)" data-payment="{{ $payment->toJson() }}">📄</button>
                            <button class="btn btn-sm btn-danger" title="Delete" onclick="confirmDelete('/payments/{{ $payment->id }}', 'payment')">🗑️</button>
                        </div>
                    </td>
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
                <div class="form-group">
                    <label class="form-label">Student *</label>
                    <select name="student_id" id="studentSelect" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->student_code }} — {{ $s->first_name }} {{ $s->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Tuition Plan *</label><select name="tuition_plan_id" class="form-control" required id="planSelect">@foreach($tuitionPlans as $p)<option value="{{ $p->id }}" data-price="{{ $p->price }}" data-classroom="{{ $p->classroom }}">{{ $p->name }} — ${{ number_format($p->price,2) }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label">Classroom</label><input type="text" id="classroomInput" class="form-control" readonly></div>
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
    document.getElementById('classroomInput').value = opt.dataset.classroom || '—';
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
    const studentSelect = document.getElementById('studentSelect');
    studentSelect.disabled = false;
    studentSelect.value = "";
    document.getElementById('paymentForm').reset();
    document.getElementById('planSelect').dispatchEvent(new Event('change'));
    openModal('paymentModal');
}

function repayStudent(studentId, planId) {
    const studentSelect = document.getElementById('studentSelect');
    studentSelect.value = studentId;
    studentSelect.disabled = true;
    
    // Add hidden input if it doesn't exist to ensure student_id is sent
    let hiddenStudentInput = document.getElementById('hiddenStudentId');
    if (!hiddenStudentInput) {
        hiddenStudentInput = document.createElement('input');
        hiddenStudentInput.type = 'hidden';
        hiddenStudentInput.name = 'student_id';
        hiddenStudentInput.id = 'hiddenStudentId';
        document.getElementById('paymentForm').appendChild(hiddenStudentInput);
    }
    hiddenStudentInput.value = studentId;

    const planSelect = document.getElementById('planSelect');
    planSelect.value = planId;
    planSelect.dispatchEvent(new Event('change'));
    openModal('paymentModal');
}
document.getElementById('planSelect').dispatchEvent(new Event('change'));
document.getElementById('methodFilter').addEventListener('change', function() {
    const p = new URLSearchParams(window.location.search);
    if (this.value) p.set('method', this.value);
    else p.delete('method');
    window.location.search = p.toString();
});
let currentVoucherPayment = null;

function openReceiptVoucher(paymentData) {
    const p = typeof paymentData === 'string' ? JSON.parse(paymentData) : paymentData;
    currentVoucherPayment = p;
    
    // Header
    const genderKh = p.student.gender === 'male' ? 'ប្រុស' : 'ស្រី';
    const payDate = new Date(p.payment_date).toLocaleDateString();
    const startDate = new Date(p.start_study_date).toLocaleDateString();
    
    const fields = [
        { label: 'ឈ្មោះ (Name)', value: `${p.student.first_name} ${p.student.last_name}` },
        { label: 'ភេទ (Gender)', value: genderKh },
        { label: 'លេខវិក្កយបត្រ (Receipt #)', value: `PAY-${p.id.toString().padStart(6, '0')}` },
        { label: 'ថ្នាក់ (Class)', value: p.tuition_plan ? p.tuition_plan.classroom : '—' },
        { label: 'វេន (Turn)', value: p.student.turn || '—' },
        { label: 'អត្តលេខ (ID)', value: p.student.student_code },
        { label: 'សម្រាប់ (For)', value: p.tuition_plan ? p.tuition_plan.name : '—' },
        { label: 'គិតចាប់ពី (From)', value: startDate },
        { label: 'បង់ថ្ងៃនៅថ្ងៃទី (Date)', value: payDate }
    ];

    document.getElementById('voucherStudentHeader').innerHTML = fields.map(f => `
        <div class="info-item">
            <div class="v-label-text">${f.label}</div>
            <input type="text" class="v-value-input" value="${f.value}">
        </div>
    `).join('');

    // Clear Items
    document.getElementById('voucherItemsBody').innerHTML = '';
    addVoucherRow(`Tuition Fee - ${p.tuition_plan ? p.tuition_plan.name : '—'}`, 1, p.amount, 0);
    
    openModal('receiptVoucherModal');
}

function addVoucherRow(desc = '', qty = 1, price = 0, disc = 0) {
    const tbody = document.getElementById('voucherItemsBody');
    const rowCount = tbody.children.length + 1;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td style="border: 1px solid #ddd; text-align: center;">${rowCount}</td>
        <td style="border: 1px solid #ddd;"><input type="text" class="v-desc" value="${desc}" style="text-align: left;"></td>
        <td style="border: 1px solid #ddd;"><input type="number" class="v-qty" value="${qty}" oninput="updateVoucherTotals()"></td>
        <td style="border: 1px solid #ddd;"><input type="number" class="v-price" value="${price}" step="0.01" oninput="updateVoucherTotals()"></td>
        <td style="border: 1px solid #ddd;"><input type="number" class="v-disc" value="${disc}" oninput="updateVoucherTotals()"></td>
        <td style="border: 1px solid #ddd; text-align: center;" class="v-total-usd">0.00</td>
        <td style="border: 1px solid #ddd; text-align: center;" class="v-total-riel">0</td>
        <td style="border: 1px solid #ddd; text-align: center;"><button class="btn btn-sm" onclick="this.closest('tr').remove(); updateVoucherTotals();">❌</button></td>
    `;
    tbody.appendChild(row);
    updateVoucherTotals();
}

function updateVoucherTotals() {
    const rows = document.getElementById('voucherItemsBody').children;
    let grandUsd = 0;
    
    Array.from(rows).forEach((row, index) => {
        row.cells[0].textContent = index + 1;
        const qty = parseFloat(row.querySelector('.v-qty').value) || 0;
        const price = parseFloat(row.querySelector('.v-price').value) || 0;
        const disc = parseFloat(row.querySelector('.v-disc').value) || 0;
        
        const totalUsd = (qty * price) * (1 - disc / 100);
        const totalRiel = Math.round(totalUsd * 4100);
        
        row.querySelector('.v-total-usd').textContent = totalUsd.toFixed(2);
        row.querySelector('.v-total-riel').textContent = totalRiel.toLocaleString();
        
        grandUsd += totalUsd;
    });

    document.getElementById('voucherGrandTotalUsd').textContent = grandUsd.toFixed(2);
    document.getElementById('voucherGrandTotalRiel').textContent = Math.round(grandUsd * 4100).toLocaleString();
}

function saveVoucher() {
    // Current requirement doesn't specify backend saving, so just a "Saved" feedback for now
    alert('Voucher data prepared for printing.');
}

function printVoucherFinal() {
    const p = currentVoucherPayment;
    
    // Extract grid fields
    const gridItems = Array.from(document.getElementById('voucherStudentHeader').querySelectorAll('.info-item')).map(div => ({
        label: div.querySelector('.v-label-text').textContent,
        value: div.querySelector('.v-value-input').value
    }));

    const items = [];
    document.getElementById('voucherItemsBody').querySelectorAll('tr').forEach(row => {
        items.push({
            desc: row.querySelector('.v-desc').value,
            qty: row.querySelector('.v-qty').value,
            price: row.querySelector('.v-price').value,
            disc: row.querySelector('.v-disc').value,
            totalUsd: row.querySelector('.v-total-usd').textContent,
            totalRiel: row.querySelector('.v-total-riel').textContent
        });
    });

    const printWindow = window.open('', '_blank');
    
    let gridHtml = gridItems.map(it => `
        <div class="info-item"><span class="info-label">${it.label}</span><div class="info-value">${it.value}</div></div>
    `).join('');

    let rowsHtml = items.map((it, idx) => `
        <tr>
            <td>${idx + 1}</td>
            <td style="text-align: left;">${it.desc}</td>
            <td>${it.qty}</td>
            <td>$${parseFloat(it.price).toFixed(2)}</td>
            <td>${it.disc}%</td>
            <td>$${it.totalUsd}</td>
            <td>${it.totalRiel}៛</td>
            <td>[ ]</td>
        </tr>
    `).join('');

    const grandTotalUsd = items.reduce((sum, it) => sum + parseFloat(it.totalUsd), 0).toFixed(2);
    const grandTotalRiel = Math.round(grandTotalUsd * 4100).toLocaleString();

    const content = `
        <html>
        <head>
            <title>Receipt</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Khmer&family=Inter:wght@400;700&display=swap');
                body { font-family: 'Inter', 'Khmer', sans-serif; padding: 20px; color: #333; }
                .receipt-container { width: 100%; max-width: 800px; margin: 0 auto; }
                .receipt-header { text-align: center; margin-bottom: 20px; }
                .receipt-header h1 { margin: 0; font-size: 24px; }
                .receipt-header h2 { margin: 0; font-size: 20px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; border: 1.5px solid #000; margin-bottom: 20px; }
                .info-item { border: 0.5px solid #000; padding: 8px; min-height: 40px; }
                .info-label { font-size: 11px; color: #333; }
                .info-value { font-weight: bold; font-size: 14px; border-bottom: 1px dotted #888; }
                .item-table { width: 100%; border-collapse: collapse; border: 1.5px solid #000; }
                .item-table th, .item-table td { border: 1px solid #000; padding: 8px; text-align: center; font-size: 12px; }
                .total-row { font-weight: bold; background: #eee; }
                .signatures { display: flex; justify-content: space-between; margin-top: 60px; padding: 0 40px; }
                .signature-box { text-align: center; width: 250px; }
                .signature-line { margin-top: 50px; border-top: 1px solid #000; }
            </style>
        </head>
        <body onload="window.print()">
            <div class="receipt-container">
                <div class="receipt-header"><h1>វិក្កយបត្រទទួលប្រាក់</h1><h2>Receipt Voucher</h2></div>
                <div class="info-grid">${gridHtml}</div>
                <table class="item-table">
                    <thead><tr><th>ល.រ<br>(No)</th><th>បរិយាយ<br>(Description)</th><th>បរិមាណ<br>(Qty)</th><th>តម្លៃ ($)<br>(Price)</th><th>បញ្ចុះ (%)<br>(Disc)</th><th>សរុប ($)<br>(Total $)</th><th>សរុប (រ)<br>(Total R)</th><th>លុប</th></tr></thead>
                    <tbody>${rowsHtml}<tr class="total-row"><td colspan="5" style="text-align: right;">សរុប (Total)</td><td>$${grandTotalUsd}</td><td>${grandTotalRiel}៛</td><td></td></tr></tbody>
                </table>
                <div class="signatures">
                    <div class="signature-box"><div>ហត្ថលេខាអតិថិជន</div><div>Customer Signature</div><div class="signature-line"></div></div>
                    <div class="signature-box"><div>ហត្ថលេខាគណនេយ្យ</div><div>Cashier Signature</div><div class="signature-line"></div></div>
                </div>
            </div>
        </body>
        </html>
    `;
    printWindow.document.write(content);
    printWindow.document.close();
}

function printReceipt(paymentData) {
    // Keep this function for simple printing if needed, but the user requested editable voucher
    openReceiptVoucher(paymentData);
}

if (new URLSearchParams(window.location.search).get('action') === 'create') openModal('paymentModal');
</script>
@endpush
@endsection
