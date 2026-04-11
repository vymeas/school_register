@extends('layouts.app')
@section('title', 'Classroom Report')
@section('page-title', 'Classroom Report')

@section('content')
<style>
.cr-stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
@media(max-width:800px){ .cr-stat-grid { grid-template-columns: 1fr 1fr; } }
.cr-classroom-card {
    background: var(--bg-card,#1e293b);
    border: 1px solid var(--border-color,#2d3f55);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
}
.cr-classroom-header {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px;
    background: rgba(255,255,255,.025);
    border-bottom: 1px solid var(--border-color,#2d3f55);
    cursor: pointer;
    user-select: none;
}
.cr-classroom-header:hover { background: rgba(99,102,241,.07); }
.cr-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg,#4f46e5,#818cf8);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: #fff; font-weight: 800; flex-shrink: 0;
}
.cr-header-info { flex: 1; min-width: 0; }
.cr-header-name { font-size: 14px; font-weight: 700; color: var(--text-primary,#e2e8f0); }
.cr-header-sub  { font-size: 11px; color: var(--text-muted,#64748b); margin-top: 2px; }
.cr-header-stats { display: flex; gap: 14px; flex-shrink: 0; }
.cr-stat-chip {
    display: flex; flex-direction: column; align-items: center;
    min-width: 52px;
}
.cr-stat-chip .val { font-size: 16px; font-weight: 800; color: var(--text-primary,#e2e8f0); }
.cr-stat-chip .lbl { font-size: 10px; color: var(--text-muted,#64748b); text-transform: uppercase; letter-spacing: .4px; }
.cr-chevron { color: var(--text-muted,#64748b); font-size: 12px; transition: transform .25s; flex-shrink: 0; }
.cr-chevron.open { transform: rotate(180deg); }

.cr-body { display: none; padding: 0; }
.cr-body.open { display: block; }

.cr-student-table { width: 100%; border-collapse: collapse; }
.cr-student-table th {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: var(--text-muted,#64748b);
    padding: 10px 16px; text-align: left;
    border-bottom: 1px solid var(--border-color,#2d3f55);
    background: rgba(255,255,255,.015);
}
.cr-student-table td {
    padding: 9px 16px; font-size: 13px;
    color: var(--text-primary,#e2e8f0);
    border-bottom: 1px solid rgba(255,255,255,.04);
}
.cr-student-table tr:last-child td { border-bottom: none; }
.cr-student-table tr:hover td { background: rgba(99,102,241,.05); }
.cr-empty { padding: 20px; text-align: center; color: var(--text-muted,#64748b); font-size: 13px; }

/* Filter bar */
.cr-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border-color,#2d3f55);
    background: rgba(255,255,255,.015);
    flex-wrap: wrap;
}
.cr-filter-label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--text-muted,#64748b);
    white-space: nowrap;
}
.cr-filter-select {
    padding: 7px 12px;
    border: 1px solid var(--border-color,#2d3f55);
    border-radius: 8px;
    background: var(--bg-secondary,#0f172a);
    color: var(--text-primary,#e2e8f0);
    font-size: 13px;
    cursor: pointer;
    outline: none;
    transition: border-color .15s;
    min-width: 160px;
}
.cr-filter-select:focus { border-color: #6366f1; }
.cr-filter-sep {
    font-size: 11px; color: var(--text-muted,#475569);
}
.cr-filter-grade-wrap {
    display: flex; align-items: center; gap: 10px;
    opacity: 0; pointer-events: none;
    transition: opacity .2s;
}
.cr-filter-grade-wrap.ready { opacity: 1; pointer-events: all; }
.cr-filter-reset {
    margin-left: auto;
    padding: 6px 14px;
    border-radius: 7px;
    border: 1px solid rgba(148,163,184,.2);
    background: transparent;
    color: var(--text-muted,#94a3b8);
    font-size: 12px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: all .14s;
    white-space: nowrap;
}
.cr-filter-reset:hover { background: rgba(255,255,255,.05); color: var(--text-primary,#e2e8f0); }
.cr-active-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    background: rgba(99,102,241,.15);
    border: 1px solid rgba(99,102,241,.3);
    color: #818cf8;
    font-size: 11px; font-weight: 700;
}
/* Export action buttons */
.cr-export-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px; font-weight: 700;
    cursor: pointer; border: none;
    transition: all .15s; white-space: nowrap;
    text-decoration: none;
}
.cr-export-btn.print  { background: rgba(99,102,241,.15); color: #818cf8; border: 1px solid rgba(99,102,241,.3); }
.cr-export-btn.excel  { background: rgba(16,185,129,.12); color: #34d399;  border: 1px solid rgba(16,185,129,.3); }
.cr-export-btn.print:hover  { background: #6366f1; color: #fff; }
.cr-export-btn.excel:hover  { background: #10b981; color: #fff; }

/* ── Print layout (hidden on screen, shown when printing) ── */
.print-only { display: none; }
@media print {
    body * { visibility: hidden !important; }
    .print-only, .print-only * { visibility: visible !important; display: block !important; }
    .print-only { position: fixed; top: 0; left: 0; width: 100%; z-index: 9999; background: #fff; padding: 20px; }
    .pt-title  { font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
    .pt-sub    { font-size: 12px; color: #64748b; margin-bottom: 16px; }
    .pt-table  { width: 100%; border-collapse: collapse; font-size: 11px; }
    .pt-table th { background: #334155 !important; color: #fff !important; padding: 7px 10px; text-align: left; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .pt-table td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; color: #1e293b; }
    .pt-table tr.classroom-row td { background: #f1f5f9 !important; font-weight: 700; color: #1e293b; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .pt-table tr.classroom-row td:first-child { padding-left: 8px; }
    .pt-footer { margin-top: 12px; font-size: 11px; color: #94a3b8; text-align: right; }
    /* summary table extra styles */
    .ps-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .ps-table th { background: #1e293b !important; color: #fff !important; padding: 9px 12px; text-align: left; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ps-table td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; color: #1e293b; }
    .ps-table tr.term-heading td { background: #334155 !important; color: #fff !important; font-weight: 800; font-size: 13px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ps-table tr.grade-heading td { background: #f1f5f9 !important; font-weight: 700; color: #334155; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ps-table tr.total-row td { background: #eff6ff !important; font-weight: 700; color: #1d4ed8; border-top: 2px solid #bfdbfe; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ps-classroom-list { font-size: 11px; color: #475569; margin-top: 3px; }
}
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">🏫 Classroom Report</h3>
        <div style="display:flex; gap:8px; align-items:center;">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">← Payment Report</a>
            <button class="cr-export-btn print" onclick="printReport('detail')">🖨️ Print Detail</button>
            <button class="cr-export-btn" style="background:rgba(251,191,36,.12);color:#f59e0b;border:1px solid rgba(251,191,36,.3);" onclick="printReport('summary')">📋 Print Summary</button>
            <button class="cr-export-btn excel" onclick="exportExcel()">📊 Export Excel</button>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="cr-filter-bar">
        <span class="cr-filter-label">🔎 Filter:</span>

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
            <span class="cr-active-badge">📅 {{ $selTerm?->name ?? 'Term' }}</span>
        @endif
        @if($selGradeId)
            @php $selGrade = $grades->firstWhere('id', $selGradeId); @endphp
            <span class="cr-active-badge">📚 {{ $selGrade?->name ?? 'Grade' }}</span>
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
                <div class="empty-icon">🏫</div>
                <h3>No classrooms found</h3>
                <p>Create classrooms first to see the report.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ══ Hidden flat table used for PRINT ══ --}}
<div class="print-only" id="printArea">
    <div class="pt-title">🏫 Classroom Report</div>
    <div class="pt-sub">
        Generated: {{ now()->format('d M Y, H:i') }}
        @if($selTermId) &nbsp;|&nbsp; Term: {{ $terms->firstWhere('id',$selTermId)?->name }} @endif
        @if($selGradeId) &nbsp;|&nbsp; Grade: {{ $grades->firstWhere('id',$selGradeId)?->name }} @endif
    </div>
    <table class="pt-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Classroom</th>
                <th>Teacher</th>
                <th>Grade</th>
                <th>Term</th>
                <th>Student Code</th>
                <th>Student Name</th>
                <th>Payments</th>
                <th>Total Paid ($)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNo = 0; @endphp
            @foreach($classrooms as $classroom)
                @php
                    $cRevenue = $classroom->enrollments->sum(fn($e) => $e->payments->sum('amount'));
                @endphp
                {{-- Classroom header row --}}
                <tr class="classroom-row">
                    <td colspan="10">
                        🏫 {{ $classroom->name }}
                        &nbsp;·&nbsp; {{ $classroom->teacher->name ?? 'No Teacher' }}
                        &nbsp;·&nbsp; {{ $classroom->grade->name ?? '—' }}
                        &nbsp;·&nbsp; {{ $classroom->grade?->term?->name ?? '—' }}
                        &nbsp;&nbsp; Students: {{ $classroom->enrollments->count() }}
                        &nbsp;|&nbsp; Revenue: ${{ number_format($cRevenue, 2) }}
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
                        <td>{{ $rowNo }}</td>
                        <td>{{ $classroom->name }}</td>
                        <td>{{ $classroom->teacher->name ?? '—' }}</td>
                        <td>{{ $classroom->grade->name ?? '—' }}</td>
                        <td>{{ $classroom->grade?->term?->name ?? '—' }}</td>
                        <td>{{ $student->student_code ?? '—' }}</td>
                        <td>{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</td>
                        <td>{{ $enrollment->payments->count() }}</td>
                        <td>${{ number_format($paid, 2) }}</td>
                        <td>{{ $status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" style="color:#94a3b8; font-style:italic; padding-left:20px;">No students enrolled</td></tr>
                @endforelse
            @endforeach
        </tbody>
    </table>
    <div class="pt-footer">School Register &mdash; Classroom Report &mdash; {{ now()->format('d M Y') }}</div>
</div>

{{-- ══ Hidden SUMMARY print area (grouped by Term → Grade) ══ --}}
<div class="print-only" id="printSummary" style="display:none;">
    <div class="pt-title">🏫 Classroom Summary Report</div>
    <div class="pt-sub">
        Generated: {{ now()->format('d M Y, H:i') }}
        @if($selTermId) &nbsp;|&nbsp; Term: {{ $terms->firstWhere('id',$selTermId)?->name }} @endif
        @if($selGradeId) &nbsp;|&nbsp; Grade: {{ $grades->firstWhere('id',$selGradeId)?->name }} @endif
    </div>

    @php
        // Group classrooms by term → grade
        $grouped = [];
        foreach ($classrooms as $cr) {
            $termName  = $cr->grade?->term?->name ?? 'No Term';
            $gradeName = $cr->grade?->name ?? 'No Grade';
            $grouped[$termName][$gradeName][] = $cr;
        }
        $grandClassrooms = 0;
        $grandStudents   = 0;
    @endphp

    <table class="ps-table">
        <thead>
            <tr>
                <th>Term / Grade / Classroom</th>
                <th style="text-align:center;">Classrooms</th>
                <th style="text-align:center;">Total Students</th>
                <th style="text-align:center;">Paid Students</th>
                <th style="text-align:right;">Revenue ($)</th>
            </tr>
        </thead>
        <tbody>
        @foreach($grouped as $termName => $gradeGroups)
            @php
                $termClassrooms = collect($gradeGroups)->flatten()->count();
                $termStudents   = 0;
                $termPaid       = 0;
                $termRevenue    = 0;
                foreach ($gradeGroups as $crs) {
                    foreach ($crs as $cr) {
                        $termStudents += $cr->enrollments->count();
                        $termPaid     += $cr->enrollments->filter(fn($e) => $e->payments->isNotEmpty())->count();
                        $termRevenue  += $cr->enrollments->sum(fn($e) => $e->payments->sum('amount'));
                    }
                }
                $grandClassrooms += $termClassrooms;
                $grandStudents   += $termStudents;
            @endphp
            {{-- Term heading --}}
            <tr class="term-heading">
                <td colspan="5">📅 {{ $termName }}</td>
            </tr>
            @foreach($gradeGroups as $gradeName => $crs)
                @php
                    $gradeStudents = 0; $gradePaid = 0; $gradeRevenue = 0;
                    foreach ($crs as $cr) {
                        $gradeStudents += $cr->enrollments->count();
                        $gradePaid     += $cr->enrollments->filter(fn($e) => $e->payments->isNotEmpty())->count();
                        $gradeRevenue  += $cr->enrollments->sum(fn($e) => $e->payments->sum('amount'));
                    }
                @endphp
                {{-- Grade heading --}}
                <tr class="grade-heading">
                    <td style="padding-left:20px;">📚 {{ $gradeName }}</td>
                    <td style="text-align:center;">{{ count($crs) }}</td>
                    <td style="text-align:center;">{{ $gradeStudents }}</td>
                    <td style="text-align:center;">{{ $gradePaid }}</td>
                    <td style="text-align:right;">${{ number_format($gradeRevenue, 2) }}</td>
                </tr>
                {{-- Individual classrooms --}}
                @foreach($crs as $cr)
                    <tr>
                        <td style="padding-left:40px;">
                            🏫 {{ $cr->name }}
                            <span class="ps-classroom-list">· {{ $cr->teacher->name ?? 'No Teacher' }}</span>
                        </td>
                        <td style="text-align:center;color:#64748b;">1</td>
                        <td style="text-align:center;">{{ $cr->enrollments->count() }}</td>
                        <td style="text-align:center;">{{ $cr->enrollments->filter(fn($e) => $e->payments->isNotEmpty())->count() }}</td>
                        <td style="text-align:right;">${{ number_format($cr->enrollments->sum(fn($e) => $e->payments->sum('amount')), 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
            {{-- Term subtotal --}}
            <tr style="border-top: 1.5px solid #cbd5e1;">
                <td style="padding-left:12px; font-weight:700; color:#475569;">Subtotal — {{ $termName }}</td>
                <td style="text-align:center; font-weight:700;">{{ $termClassrooms }}</td>
                <td style="text-align:center; font-weight:700;">{{ $termStudents }}</td>
                <td style="text-align:center; font-weight:700;">{{ $termPaid }}</td>
                <td style="text-align:right; font-weight:700;">${{ number_format($termRevenue, 2) }}</td>
            </tr>
            <tr><td colspan="5" style="padding:6px;"></td></tr>
        @endforeach
        {{-- Grand total --}}
        <tr class="total-row">
            <td style="font-size:13px;">🏆 GRAND TOTAL</td>
            <td style="text-align:center; font-size:13px;">{{ $grandClassrooms }}</td>
            <td style="text-align:center; font-size:13px;">{{ $grandStudents }}</td>
            <td style="text-align:center;">—</td>
            <td style="text-align:right; font-size:13px;">
                ${{ number_format($classrooms->sum(fn($c) => $c->enrollments->sum(fn($e) => $e->payments->sum('amount'))), 2) }}
            </td>
        </tr>
        </tbody>
    </table>
    <div class="pt-footer">School Register &mdash; Classroom Summary &mdash; {{ now()->format('d M Y') }}</div>
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
    const table  = document.querySelector('#printArea .pt-table');
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
    XLSX.utils.book_append_sheet(wb, ws, 'Classroom Report');
    XLSX.writeFile(wb, `classroom-report-${stamp}.xlsx`);
}
</script>
@endpush
@endsection
