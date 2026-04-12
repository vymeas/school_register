@extends('layouts.app')
@section('title', 'Classroom Report')
@section('page-title', 'Classroom Report')

@section('content')
<style>
/* ── Report Container ── */
.cr-stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width:900px){ .cr-stat-grid { grid-template-columns: 1fr 1fr; } }

.cr-classroom-card {
    background: var(--bg-card,#ffffff);
    border: 1px solid var(--border-color,#e2e8f0);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.cr-classroom-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

.cr-classroom-header {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 20px;
    background: var(--bg-header, rgba(0,0,0,.01));
    border-bottom: 1px solid var(--border-color,#e2e8f0);
    cursor: pointer;
    user-select: none;
}
.cr-classroom-header:hover { background: rgba(99,102,241,.05); }

.cr-avatar {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #fff; font-weight: 800; flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(99,102,241,0.2);
}
.cr-header-info { flex: 1; min-width: 0; }
.cr-header-name { font-size: 15px; font-weight: 700; color: var(--text-primary,#1e293b); }
.cr-header-sub  { font-size: 12px; color: var(--text-muted,#64748b); margin-top: 4px; }
.cr-header-stats { display: flex; gap: 20px; flex-shrink: 0; margin-right: 12px; }
.cr-stat-chip {
    display: flex; flex-direction: column; align-items: center;
    min-width: 60px;
}
.cr-stat-chip .val { font-size: 18px; font-weight: 800; color: var(--text-primary,#1e293b); }
.cr-stat-chip .lbl { font-size: 10px; color: var(--text-muted,#94a3b8); text-transform: uppercase; letter-spacing: .6px; font-weight: 600; }

.cr-chevron { color: var(--text-muted,#94a3b8); font-size: 14px; transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1); flex-shrink: 0; }
.cr-chevron.open { transform: rotate(180deg); color: #6366f1; }

.cr-body { display: none; padding: 0; animation: slideDown 0.3s ease-out; }
.cr-body.open { display: block; }
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

.cr-student-table { width: 100%; border-collapse: collapse; }
.cr-student-table th {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: var(--text-muted,#64748b);
    padding: 12px 20px; text-align: left;
    border-bottom: 2px solid var(--border-color,#f1f5f9);
    background: var(--bg-table-header,#f8fafc);
}
.cr-student-table td {
    padding: 12px 20px; font-size: 14px;
    color: var(--text-primary,#1e293b);
    border-bottom: 1px solid var(--border-color,#f1f5f9);
}
.cr-student-table tr:hover td { background: rgba(99,102,241,.03); }

/* ── Filter Bar ── */
.cr-filter-bar {
    display: flex; align-items: center; gap: 12px;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color,#e2e8f0);
    background: #f8fafc;
    flex-wrap: wrap;
}
.cr-filter-label { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-right: 4px; }
.cr-filter-select {
    padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 10px;
    background: #fff; color: #1e293b; font-size: 13px; font-weight: 500;
    cursor: pointer; outline: none; transition: all .2s; min-width: 180px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.cr-filter-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.cr-active-badge {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
    border-radius: 20px; background: #eef2ff; border: 1px solid #e0e7ff; color: #4f46e5;
    font-size: 12px; font-weight: 600;
}
.cr-filter-reset {
    padding: 6px 14px; border-radius: 10px; border: 1px solid #e2e8f0;
    background: #fff; color: #ef4444; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 4px;
}
.cr-filter-reset:hover { background: #fef2f2; border-color: #fecaca; }

/* ── Export Buttons ── */
.cr-export-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; border-radius: 10px; font-size: 13px;
    font-weight: 600; cursor: pointer; border: 1px solid transparent;
    transition: all .2s; white-space: nowrap; text-decoration: none;
}
.cr-export-btn.print { background: #6366f1; color: #fff; box-shadow: 0 2px 4px rgba(99,102,241,0.2); }
.cr-export-btn.print:hover { background: #4f46e5; transform: translateY(-1px); }
.cr-export-btn.summary { background: #fff; color: #4b5563; border-color: #e5e7eb; }
.cr-export-btn.summary:hover { background: #f9fafb; border-color: #d1d5db; }
.cr-export-btn.excel { background: #10b981; color: #fff; box-shadow: 0 2px 4px rgba(16,185,129,0.2); }
.cr-export-btn.excel:hover { background: #059669; transform: translateY(-1px); }

/* ── Hide Print Elements on Screen ── */
.print-only { display: none !important; }

/* ── Hide Print Elements on Screen ── */
.print-only { display: none !important; }

/* ── PRINT STYLES (Excel-like Grid) ── */
@media print {
    @page { margin: 10mm; size: A4; }
    body * { visibility: hidden !important; }
    .print-only { visibility: visible !important; display: block !important; position: absolute; top: 0; left: 0; width: 100%; background: #fff; padding: 0; }
    .print-only * { visibility: visible !important; }
    
    .pt-title { font-size: 20px; font-weight: bold; color: #000; margin-bottom: 2px; }
    .pt-sub   { font-size: 11px; color: #333; margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 5px; }
    
    .print-table { width: 100%; border-collapse: collapse; border: 1px solid #000; table-layout: auto; }
    .print-table th, .print-table td { border: 1px solid #000; padding: 4px 6px; font-size: 10px; color: #000; line-height: 1.2; }
    .print-table th { background: #f2f2f2 !important; font-weight: bold; text-align: left; -webkit-print-color-adjust: exact; }
    
    /* Excel Grouping Style */
    .row-group-term  { background: #e2e8f0 !important; font-weight: bold; font-size: 11px; -webkit-print-color-adjust: exact; }
    .row-group-grade { background: #f1f5f9 !important; font-weight: bold; font-size: 10px; -webkit-print-color-adjust: exact; }
    .row-subtotal    { background: #fafafa !important; font-weight: bold; -webkit-print-color-adjust: exact; }
    .row-grand       { background: #000 !important; color: #fff !important; font-weight: bold; font-size: 11px; -webkit-print-color-adjust: exact; }
    
    .pt-footer { margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 4px; font-size: 9px; color: #666; text-align: center; }
}

</style>

<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h3 class="card-title" style="margin:0;">Classroom Report</h3>
        <div style="display:flex; gap:10px; align-items:center;">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back</a>
            <button class="cr-export-btn print" onclick="printReport('detail')"><i data-lucide="printer"></i> Detail</button>
            <button class="cr-export-btn summary" onclick="printReport('summary')"><i data-lucide="file-text"></i> Summary</button>
            <button class="cr-export-btn excel" onclick="exportExcel()"><i data-lucide="file-spreadsheet"></i> Excel</button>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="cr-filter-bar">
        <span class="cr-filter-label">Filter:</span>

        {{-- Term select --}}
        <select id="termFilter" class="cr-filter-select" onchange="applyTermFilter(this.value)">
            <option value="">All Terms</option>
            @foreach($terms as $term)
                <option value="{{ $term->id }}" {{ $selTermId == $term->id ? 'selected' : '' }}>
                    {{ $term->name }}
                    @if($term->status === 'active') ★ @endif
                </option>
            @endforeach
        </select>

        {{-- Cascading grade select (appears once a term is chosen) --}}
        <div class="cr-filter-grade-wrap {{ $selTermId ? 'ready' : '' }}" id="gradeWrap">
            <span class="cr-filter-sep">›</span>
            <select id="gradeFilter" class="cr-filter-select" onchange="applyGradeFilter(this.value)">
                <option value="">All Grades</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ $selGradeId == $grade->id ? 'selected' : '' }}>
                        {{ $grade->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Active filter badges --}}
        @if($selTermId)
            @php $selTerm = $terms->firstWhere('id', $selTermId); @endphp
            <span class="cr-active-badge">{{ $selTerm?->name ?? 'Term' }}</span>
        @endif
        @if($selGradeId)
            @php $selGrade = $grades->firstWhere('id', $selGradeId); @endphp
            <span class="cr-active-badge">{{ $selGrade?->name ?? 'Grade' }}</span>
        @endif

        @if($selTermId || $selGradeId)
            <a href="{{ route('reports.classroom') }}" class="cr-filter-reset">✕ Clear Filters</a>
        @endif

        {{-- Result count --}}
        <span style="margin-left:auto; font-size:12px; color:var(--text-muted,#64748b);">
            {{ $classrooms->count() }} classroom{{ $classrooms->count() != 1 ? 's' : '' }} shown
        </span>
    </div>

    {{-- Summary stats --}}
    @php
        $totalClassrooms  = $classrooms->count();
        $totalStudents    = $classrooms->sum(fn($c) => $c->enrollments->count());
        $totalRevenue     = $classrooms->sum(fn($c) => $c->enrollments->sum(fn($e) => $e->payments->sum('amount')));
        $avgPerClass      = $totalClassrooms ? round($totalStudents / $totalClassrooms, 1) : 0;
    @endphp
    <div class="card-body" style="padding-top:14px; padding-bottom:0;">
        <div class="cr-stat-grid">
            <div class="stat-card">
                <div class="stat-label">Classrooms</div>
                <div class="stat-value">{{ $totalClassrooms }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Students</div>
                <div class="stat-value">{{ $totalStudents }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg Students/Class</div>
                <div class="stat-value">{{ $avgPerClass }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Classroom cards --}}
    <div class="card-body" style="padding-top: 0;">
        @forelse($classrooms as $i => $classroom)
            @php
                $studentCount = $classroom->enrollments->count();
                $revenue = $classroom->enrollments->sum(fn($e) => $e->payments->sum('amount'));
                $paidCount = $classroom->enrollments->filter(fn($e) => $e->payments->isNotEmpty())->count();
            @endphp
            <div class="cr-classroom-card">
                <div class="cr-classroom-header" onclick="toggleClass('cc{{ $i }}')">
                    <div class="cr-avatar">{{ strtoupper(substr($classroom->name, 0, 2)) }}</div>
                    <div class="cr-header-info">
                        <div class="cr-header-name">{{ $classroom->name }}</div>
                        <div class="cr-header-sub">
                            {{ $classroom->teacher->name ?? 'No Teacher' }}
                            @if($classroom->grade) · {{ $classroom->grade->name }} @endif
                            @if($classroom->grade?->term) · {{ $classroom->grade->term->name }} @endif
                            @if($classroom->turn) · {{ $classroom->turn->name }} @endif
                        </div>
                    </div>
                    <div class="cr-header-stats">
                        <div class="cr-stat-chip">
                            <span class="val">{{ $studentCount }}</span>
                            <span class="lbl">Students</span>
                        </div>
                        <div class="cr-stat-chip">
                            <span class="val">{{ $paidCount }}</span>
                            <span class="lbl">Paid</span>
                        </div>
                        <div class="cr-stat-chip">
                            <span class="val" style="color:#34d399;">${{ number_format($revenue, 0) }}</span>
                            <span class="lbl">Revenue</span>
                        </div>
                    </div>
                    <span class="cr-chevron" id="chev-cc{{ $i }}">▼</span>
                </div>
                <div class="cr-body" id="cc{{ $i }}">
                    @if($classroom->enrollments->isEmpty())
                        <div class="cr-empty">No students enrolled in this classroom.</div>
                    @else
                        <table class="cr-student-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Code</th>
                                    <th>Payments</th>
                                    <th>Total Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classroom->enrollments as $j => $enrollment)
                                    @php
                                        $student = $enrollment->student;
                                        $paid = $enrollment->payments->sum('amount');
                                        $lastPay = $enrollment->payments->sortByDesc('payment_date')->first();
                                        $paidUntil = $lastPay?->end_study_date;
                                        $isActive = $paidUntil && now()->lte($paidUntil);
                                    @endphp
                                    <tr>
                                        <td style="color:var(--text-muted,#64748b); font-size:12px;">{{ $j + 1 }}</td>
                                        <td>
                                            <strong>{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</strong>
                                        </td>
                                        <td><span class="badge secondary" style="font-size:10px;">{{ $student->student_code ?? '—' }}</span></td>
                                        <td style="color:var(--text-muted,#64748b);">{{ $enrollment->payments->count() }}</td>
                                        <td><strong style="color:#34d399;">${{ number_format($paid, 2) }}</strong></td>
                                        <td>
                                            @if($paidUntil)
                                                <span class="badge {{ $isActive ? 'active' : 'expired' }}">{{ $isActive ? 'Active' : 'Expired' }}</span>
                                            @else
                                                <span class="badge" style="background:rgba(100,116,139,.15);color:#94a3b8;">No Payment</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i data-lucide="school"></i></div>
                <h3>No classrooms found</h3>
                <p>Create classrooms first to see the report.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ══ Hidden flat table used for PRINT ══ --}}
<div class="print-only" id="printArea">
    <div class="pt-title">Classroom Detailed Report</div>
    <div class="pt-sub">
        Generated: {{ now()->format('d M Y, H:i') }}
        @if($selTermId) &nbsp;|&nbsp; Term: {{ $terms->firstWhere('id',$selTermId)?->name }} @endif
        @if($selGradeId) &nbsp;|&nbsp; Grade: {{ $grades->firstWhere('id',$selGradeId)?->name }} @endif
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th style="width:100px;">Classroom</th>
                <th style="width:120px;">Student Name</th>
                <th style="width:80px;">Code</th>
                <th style="width:100px;">Teacher</th>
                <th style="width:70px;">Grade</th>
                <th style="width:60px;">Payments</th>
                <th style="width:80px;">Paid ($)</th>
                <th style="width:80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNo = 0; @endphp
            @foreach($classrooms as $classroom)
                @php $cRevenue = $classroom->enrollments->sum(fn($e) => $e->payments->sum('amount')); @endphp
                <tr style="background:#f8fafc; font-weight:700;">
                    <td colspan="9" style="background:#f1f5f9 !important; -webkit-print-color-adjust:exact;">
                        {{ $classroom->name }} &nbsp;·&nbsp; {{ $classroom->teacher->name ?? 'No Teacher' }}
                        &nbsp;·&nbsp; {{ $classroom->grade->name ?? '—' }} ({{ $classroom->grade?->term?->name ?? '—' }})
                        &nbsp;·&nbsp; {{ $classroom->enrollments->count() }} Students &nbsp;·&nbsp; ${{ number_format($cRevenue, 2) }} Revenue
                    </td>
                </tr>
                @forelse($classroom->enrollments as $enrollment)
                    @php
                        $rowNo++;
                        $student  = $enrollment->student;
                        $paid     = $enrollment->payments->sum('amount');
                        $lastPay  = $enrollment->payments->sortByDesc('payment_date')->first();
                        $paidUntil = $lastPay?->end_study_date;
                        $isActive = $paidUntil && now()->lte($paidUntil);
                        $status   = $paidUntil ? ($isActive ? 'Active' : 'Expired') : 'No Payment';
                    @endphp
                    <tr>
                        <td style="text-align:center;">{{ $rowNo }}</td>
                        <td>{{ $classroom->name }}</td>
                        <td><strong>{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</strong></td>
                        <td>{{ $student->student_code ?? '—' }}</td>
                        <td>{{ $classroom->teacher->name ?? '—' }}</td>
                        <td>{{ $classroom->grade->name ?? '—' }}</td>
                        <td style="text-align:center;">{{ $enrollment->payments->count() }}</td>
                        <td style="text-align:right;">${{ number_format($paid, 2) }}</td>
                        <td style="text-align:center;">{{ $status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="color:#64748b; font-style:italic; text-align:center;">No students enrolled</td></tr>
                @endforelse
            @endforeach
        </tbody>
    </table>
    <div class="pt-footer">School Management System &bull; Classroom Detailed Report &bull; {{ now()->format('d M Y') }}</div>
</div>

{{-- ══ Hidden SUMMARY print area (grouped by Term → Grade) ══ --}}
<div class="print-only" id="printSummary" style="display:none;">
    <div class="pt-title">CLASSROOM SUMMARY REPORT</div>
    <div class="pt-sub">
        Generated: {{ now()->format('d M Y, H:i') }}
        @if($selTermId) &nbsp;|&nbsp; Term: {{ $terms->firstWhere('id',$selTermId)?->name }} @endif
        @if($selGradeId) &nbsp;|&nbsp; Grade: {{ $grades->firstWhere('id',$selGradeId)?->name }} @endif
    </div>

    @php
        $grouped = [];
        foreach ($classrooms as $cr) {
            $termName  = $cr->grade?->term?->name ?? 'No Term';
            $gradeName = $cr->grade?->name ?? 'No Grade';
            $grouped[$termName][$gradeName][] = $cr;
        }
        $grandRev = 0; $grandStu = 0; $grandCls = 0;
    @endphp

    <table class="print-table">
        <thead>
            <tr>
                <th style="width:40px; text-align:center;">#</th>
                <th>Term / Grade / Classroom</th>
                <th style="width:120px;">Teacher</th>
                <th style="width:60px; text-align:center;">Students</th>
                <th style="width:60px; text-align:center;">Paid</th>
                <th style="width:90px; text-align:right;">Revenue ($)</th>
            </tr>
        </thead>
        <tbody>
        @foreach($grouped as $termName => $gradeGroups)
            @php $termRev = 0; $termStu = 0; $termCls = 0; @endphp
            <tr class="row-group-term">
                <td colspan="6">Academic Term: {{ $termName }}</td>
            </tr>
            @foreach($gradeGroups as $gradeName => $crs)
                @php $gradeStu = 0; $gradePaid = 0; $gradeRev = 0; @endphp
                <tr class="row-group-grade">
                    <td colspan="6" style="padding-left:14px;">Grade: {{ $gradeName }}</td>
                </tr>
                @foreach($crs as $idx => $cr)
                    @php
                        $cStu = $cr->enrollments->count();
                        $cPaid = $cr->enrollments->filter(fn($e) => $e->payments->isNotEmpty())->count();
                        $cRev = $cr->enrollments->sum(fn($e) => $e->payments->sum('amount'));
                        $gradeStu += $cStu; $gradePaid += $cPaid; $gradeRev += $cRev;
                    @endphp
                    <tr>
                        <td style="text-align:center;">{{ $idx + 1 }}</td>
                        <td style="padding-left:24px;">{{ $cr->name }}</td>
                        <td>{{ $cr->teacher->name ?? '—' }}</td>
                        <td style="text-align:center;">{{ $cStu }}</td>
                        <td style="text-align:center;">{{ $cPaid }}</td>
                        <td style="text-align:right;">{{ number_format($cRev, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="row-subtotal">
                    <td colspan="3" style="text-align:right;">Subtotal {{ $gradeName }}:</td>
                    <td style="text-align:center;">{{ $gradeStu }}</td>
                    <td style="text-align:center;">{{ $gradePaid }}</td>
                    <td style="text-align:right;">{{ number_format($gradeRev, 2) }}</td>
                </tr>
                @php $termRev += $gradeRev; $termStu += $gradeStu; $termCls += count($crs); @endphp
            @endforeach
            <tr class="row-subtotal" style="border-top: 1.5px solid #000;">
                <td colspan="3" style="text-align:right;">Total {{ $termName }}:</td>
                <td style="text-align:center;">{{ $termStu }}</td>
                <td></td>
                <td style="text-align:right;">{{ number_format($termRev, 2) }}</td>
            </tr>
            @php $grandRev += $termRev; $grandStu += $termStu; $grandCls += $termCls; @endphp
        @endforeach
        </tbody>
        <tfoot>
            <tr class="row-grand">
                <td colspan="3" style="text-align:right;">GRAND TOTAL:</td>
                <td style="text-align:center;">{{ $grandStu }}</td>
                <td style="text-align:center;">—</td>
                <td style="text-align:right;">{{ number_format($grandRev, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    <div class="pt-footer">School Management System &bull; Summary Report &bull; {{ now()->format('d M Y') }}</div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function toggleClass(id) {
    const body = document.getElementById(id);
    const chev = document.getElementById('chev-' + id);
    const open = body.classList.toggle('open');
    chev.classList.toggle('open', open);
}

// ── Term filter ──
function applyTermFilter(termId) {
    const p = new URLSearchParams(window.location.search);
    termId ? p.set('term_id', termId) : p.delete('term_id');
    p.delete('grade_id');
    window.location.search = p.toString();
}
// ── Grade filter ──
function applyGradeFilter(gradeId) {
    const p = new URLSearchParams(window.location.search);
    gradeId ? p.set('grade_id', gradeId) : p.delete('grade_id');
    window.location.search = p.toString();
}

// ── Print (mode: 'detail' | 'summary') ──
function printReport(mode) {
    const detail  = document.getElementById('printArea');
    const summary = document.getElementById('printSummary');
    if (mode === 'summary') {
        detail.style.display  = 'none';
        summary.style.display = 'block';
    } else {
        detail.style.display  = 'block';
        summary.style.display = 'none';
    }
    window.print();
    // restore after print dialog closes
    setTimeout(() => {
        detail.style.display  = 'none';
        summary.style.display = 'none';
    }, 1000);
}

// ── Export to Excel using SheetJS ──
function exportExcel() {
    // Target the detailed report table
    const table  = document.querySelector('#printArea .print-table');
    if (!table) {
        alert('Report table not found.');
        return;
    }
    const wb     = XLSX.utils.book_new();
    const ws     = XLSX.utils.table_to_sheet(table);

    // Auto column widths
    const range  = XLSX.utils.decode_range(ws['!ref']);
    const widths = [];
    for (let C = range.s.c; C <= range.e.c; C++) {
        let max = 10;
        for (let R = range.s.r; R <= range.e.r; R++) {
            const cell = ws[XLSX.utils.encode_cell({ r: R, c: C })];
            if (cell && cell.v) max = Math.max(max, String(cell.v).length + 2);
        }
        widths.push({ wch: Math.min(max, 40) });
    }
    ws['!cols'] = widths;

    const now    = new Date();
    const stamp  = `${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}`;
    XLSX.utils.book_append_sheet(wb, ws, 'Classroom Detailed Report');
    XLSX.writeFile(wb, `classroom-report-${stamp}.xlsx`);
}
</script>
@endpush
@endsection
