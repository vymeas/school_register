@extends('layouts.app')
@section('title', 'Not Paid Classrooms')
@section('page-title', 'Not Paid Classrooms')

@section('content')
<style>
.up-card {
    background: var(--bg-card,#1e293b);
    border: 1px solid var(--border-color,#2d3f55);
    border-radius: 12px; overflow: hidden;
    margin-bottom: 14px;
}
.up-header {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color,#2d3f55);
    cursor: pointer; user-select: none;
    background: rgba(255,255,255,.02);
    transition: background .14s;
}
.up-header:hover { background: rgba(239,68,68,.06); }
.up-av {
    width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
    background: linear-gradient(135deg,#b91c1c,#f87171);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800; color: #fff;
}
.up-info { flex: 1; min-width: 0; }
.up-name { font-size: 14px; font-weight: 700; color: var(--text-primary,#e2e8f0); }
.up-sub  { font-size: 11px; color: var(--text-muted,#64748b); margin-top:2px; }
.up-chips { display: flex; gap: 10px; flex-shrink: 0; }
.up-chip {
    display: flex; flex-direction: column; align-items: center; min-width: 50px;
}
.up-chip .val { font-size: 16px; font-weight: 800; }
.up-chip .lbl { font-size: 10px; color: var(--text-muted,#64748b); text-transform: uppercase; letter-spacing:.4px; }
.up-chip .val.danger { color: #f87171; }
.up-chevron { font-size: 11px; color: var(--text-muted,#64748b); transition: transform .2s; }
.up-chevron.open { transform: rotate(180deg); }

.up-body { display: none; }
.up-body.open { display: block; }

.up-table { width: 100%; border-collapse: collapse; }
.up-table th {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: var(--text-muted,#64748b);
    padding: 9px 14px; text-align: left;
    border-bottom: 1px solid var(--border-color,#2d3f55);
    background: rgba(239,68,68,.04);
}
.up-table td {
    padding: 8px 14px; font-size: 13px;
    color: var(--text-primary,#e2e8f0);
    border-bottom: 1px solid rgba(255,255,255,.04);
}
.up-table tr:last-child td { border-bottom: none; }
.up-table tr:hover td { background: rgba(239,68,68,.05); }
.up-expired-tag {
    display:inline-flex; align-items:center; gap:3px; padding:2px 8px;
    border-radius:20px; font-size:10px; font-weight:700;
    background:rgba(239,68,68,.12); color:#f87171; border:1px solid rgba(239,68,68,.2);
}
.up-never-tag {
    display:inline-flex; align-items:center; gap:3px; padding:2px 8px;
    border-radius:20px; font-size:10px; font-weight:700;
    background:rgba(100,116,139,.12); color:#94a3b8; border:1px solid rgba(100,116,139,.2);
}
.up-alert-bar {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 16px; margin-bottom: 16px;
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.22);
    border-radius: 10px; font-size: 13px;
    color: var(--text-primary,#e2e8f0);
}

/* Print */
@media print {
    body * { visibility: hidden !important; }
    .up-print-area, .up-print-area * { visibility: visible !important; }
    .up-print-area {
        position: fixed; top:0; left:0; width:100%;
        padding:20px; background:#fff; display: block !important;
    }
    .up-print-area table { width:100%; border-collapse:collapse; font-size:11px; }
    .up-print-area th { background:#334155!important; color:#fff!important; padding:7px 10px; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .up-print-area td { padding:6px 10px; border-bottom:1px solid #e2e8f0; color:#1e293b; }
    .up-print-area tr.cr-row td { background:#f1f5f9!important; font-weight:700; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .up-print-area tfoot td { background:#fee2e2!important; font-weight:700; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .up-print-area .pt-title { font-size:16px; font-weight:800; color:#1e293b; margin-bottom:4px; }
    .up-print-area .pt-sub   { font-size:11px; color:#64748b; margin-bottom:14px; }
}
.up-print-area { display: none; }
</style>

<div class="card" style="width:100%;height:100%;">
    <div class="card-header">
        <h3 class="card-title">🚨 Not Paid Classrooms</h3>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="{{ route('reports.classroom') }}" class="btn btn-secondary btn-sm">← Classroom Report</a>
            <button class="btn btn-secondary btn-sm" onclick="window.print()" style="border-color:rgba(99,102,241,.4);color:#818cf8;">🖨️ Print</button>
            <button class="btn btn-secondary btn-sm" onclick="exportUnpaidCsv()" style="border-color:rgba(16,185,129,.4);color:#34d399;">📊 Export</button>
        </div>
    </div>

    <div class="card-body">
        {{-- Alert bar --}}
        <div class="up-alert-bar">
            <span style="font-size:20px;">⚠️</span>
            <div>
                <strong>{{ $classrooms->count() }}</strong> classroom{{ $classrooms->count() != 1 ? 's' : '' }}
                have students who have <strong style="color:#f87171;">not yet paid</strong> or have <strong style="color:#f87171;">expired payments</strong>.
                Total unpaid students: <strong style="color:#f87171;">{{ $totalUnpaidStudents }}</strong>
            </div>
        </div>

        @forelse($classrooms as $i => $classroom)
            @php $cnt = $classroom->unpaidEnrollments->count(); @endphp
            <div class="up-card">
                <div class="up-header" onclick="toggleUp('up{{ $i }}')">
                    <div class="up-av">{{ strtoupper(substr($classroom->name,0,2)) }}</div>
                    <div class="up-info">
                        <div class="up-name">{{ $classroom->name }}</div>
                        <div class="up-sub">
                            {{ $classroom->teacher->name ?? 'No Teacher' }}
                            @if($classroom->grade) · {{ $classroom->grade->name }} @endif
                            @if($classroom->grade?->term) · {{ $classroom->grade->term->name }} @endif
                        </div>
                    </div>
                    <div class="up-chips">
                        <div class="up-chip">
                            <span class="val danger">{{ $cnt }}</span>
                            <span class="lbl">Unpaid</span>
                        </div>
                        <div class="up-chip">
                            <span class="val">{{ $classroom->enrollments->count() }}</span>
                            <span class="lbl">Total</span>
                        </div>
                    </div>
                    <span class="up-chevron" id="chev-up{{ $i }}">▼</span>
                </div>
                <div class="up-body" id="up{{ $i }}">
                    <table class="up-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Code</th>
                                <th>Last Payment</th>
                                <th>Paid Until</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classroom->unpaidEnrollments as $j => $enrollment)
                                @php
                                    $student  = $enrollment->student;
                                    $lastPay  = $enrollment->payments->sortByDesc('payment_date')->first();
                                    $paidUntil = $lastPay?->end_study_date;
                                    $isExpired = $paidUntil && now()->gt($paidUntil);
                                @endphp
                                <tr>
                                    <td style="color:var(--text-muted,#64748b); font-size:11px;">{{ $j + 1 }}</td>
                                    <td><strong>{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</strong></td>
                                    <td><span class="badge secondary" style="font-size:10px;">{{ $student->student_code ?? '—' }}</span></td>
                                    <td style="color:var(--text-muted,#64748b);">
                                        {{ $lastPay?->payment_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td>
                                        @if($paidUntil)
                                            <span style="color:#f87171;">{{ \Carbon\Carbon::parse($paidUntil)->format('d M Y') }}</span>
                                        @else
                                            <span style="color:#94a3b8;">Never paid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$lastPay)
                                            <span class="up-never-tag">⊘ No Payment</span>
                                        @else
                                            <span class="up-expired-tag">↩ Expired</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">✅</div>
                <h3>All students are paid!</h3>
                <p>No classrooms with unpaid or expired payment students.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Print area --}}
<div class="up-print-area" id="upPrintArea">
    <div class="pt-title">🚨 Not Paid Classrooms Report</div>
    <div class="pt-sub">Generated: {{ now()->format('d M Y, H:i') }} · {{ $classrooms->count() }} classrooms · {{ $totalUnpaidStudents }} unpaid students</div>
    <table>
        <thead>
            <tr><th>#</th><th>Classroom</th><th>Grade</th><th>Term</th><th>Student</th><th>Code</th><th>Last Payment</th><th>Paid Until</th><th>Reason</th></tr>
        </thead>
        <tbody>
            @php $rn = 0; @endphp
            @foreach($classrooms as $classroom)
                <tr class="cr-row">
                    <td colspan="9">
                        🏫 {{ $classroom->name }}
                        · {{ $classroom->teacher->name ?? 'No Teacher' }}
                        · {{ $classroom->grade->name ?? '—' }}
                        · Unpaid: {{ $classroom->unpaidEnrollments->count() }} / {{ $classroom->enrollments->count() }}
                    </td>
                </tr>
                @foreach($classroom->unpaidEnrollments as $enrollment)
                    @php
                        $rn++;
                        $student  = $enrollment->student;
                        $lastPay  = $enrollment->payments->sortByDesc('payment_date')->first();
                        $paidUntil = $lastPay?->end_study_date;
                    @endphp
                    <tr>
                        <td>{{ $rn }}</td>
                        <td>{{ $classroom->name }}</td>
                        <td>{{ $classroom->grade->name ?? '—' }}</td>
                        <td>{{ $classroom->grade?->term?->name ?? '—' }}</td>
                        <td>{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</td>
                        <td>{{ $student->student_code ?? '—' }}</td>
                        <td>{{ $lastPay?->payment_date?->format('d M Y') ?? '—' }}</td>
                        <td>{{ $paidUntil ? \Carbon\Carbon::parse($paidUntil)->format('d M Y') : 'Never' }}</td>
                        <td>{{ !$lastPay ? 'No Payment' : 'Expired' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="4"><strong>TOTAL</strong></td><td colspan="5"><strong>{{ $totalUnpaidStudents }} unpaid students in {{ $classrooms->count() }} classrooms</strong></td></tr>
        </tfoot>
    </table>
</div>

@push('scripts')
<script>
function toggleUp(id) {
    const body  = document.getElementById(id);
    const chev  = document.getElementById('chev-' + id);
    const open  = body.classList.toggle('open');
    chev.classList.toggle('open', open);
}

function exportUnpaidCsv() {
    const headers = ['#','Classroom','Grade','Term','Student','Code','Last Payment','Paid Until','Reason'];
    const lines   = [headers.join(',')];
    let rn = 0;
    @foreach($classrooms as $classroom)
        @foreach($classroom->unpaidEnrollments as $enrollment)
            @php
                $s  = $enrollment->student;
                $lp = $enrollment->payments->sortByDesc('payment_date')->first();
                $pu = $lp?->end_study_date;
            @endphp
            lines.push([
                ++rn,
                '"{{ addslashes($classroom->name) }}"',
                '"{{ addslashes($classroom->grade->name ?? '—') }}"',
                '"{{ addslashes($classroom->grade?->term?->name ?? '—') }}"',
                '"{{ addslashes(($s->first_name ?? '') . ' ' . ($s->last_name ?? '')) }}"',
                '"{{ $s->student_code ?? '—' }}"',
                '"{{ $lp?->payment_date?->format('d M Y') ?? '—' }}"',
                '"{{ $pu ? \Carbon\Carbon::parse($pu)->format('d M Y') : 'Never' }}"',
                '"{{ !$lp ? 'No Payment' : 'Expired' }}"',
            ].join(','));
        @endforeach
    @endforeach
    const blob = new Blob(['\uFEFF' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'unpaid-classrooms-{{ now()->format("Ymd") }}.csv';
    a.click();
}
</script>
@endpush
@endsection
