@extends('layouts.app')
@section('title', 'Classroom Summary')
@section('page-title', 'Classroom Summary')

@section('content')
<style>
/* ── Excel-style datatable ── */
.cs-toolbar {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color,#e2e8f0);
    background: var(--bg-body,#f1f5f9);
    flex-wrap: wrap;
}
.cs-search-wrap {
    display: flex; align-items: center;
    border: 1px solid var(--border-color,#e2e8f0);
    border-radius: 8px;
    background: var(--bg-input,#ffffff);
    padding: 6px 12px; gap: 8px; flex: 1; max-width: 300px;
}
.cs-search-wrap input {
    border: none; background: transparent;
    color: var(--text-primary,#1c2434); font-size: 13px; outline: none; width: 100%;
}
.cs-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 8px; font-size: 12px;
    font-weight: 700; cursor: pointer; border: none;
    transition: all .15s; white-space: nowrap;
}
.cs-btn.print  { background: rgba(99,102,241,.15); color:#6366f1; border:1px solid rgba(99,102,241,.3); }
.cs-btn.excel  { background: rgba(16,185,129,.12);  color:#059669;  border:1px solid rgba(16,185,129,.3); }
.cs-btn.print:hover { background:#6366f1; color:#fff; }
.cs-btn.excel:hover { background:#10b981; color:#fff; }
.cs-count { margin-left:auto; font-size:12px; color:var(--text-muted,#64748b); }

/* Spreadsheet table */
.cs-table-wrap { overflow-x: auto; }
.cs-table {
    width: 100%; border-collapse: collapse;
    font-size: 13px;
}
.cs-table thead th {
    position: sticky; top: 0; z-index: 2;
    background: var(--bg-table-header,#f9fafb);
    color: var(--text-muted,#64748b);
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    padding: 10px 14px; text-align: left;
    border-bottom: 2px solid var(--border-color,#e2e8f0);
    white-space: nowrap; cursor: pointer; user-select: none;
}
.cs-table thead th:hover { color: var(--text-primary,#1c2434); }
.cs-table thead th .sort-icon { margin-left: 4px; opacity: .4; font-size: 9px; }
.cs-table thead th.sorted .sort-icon { opacity: 1; color: #6366f1; }
.cs-table tbody tr {
    border-bottom: 1px solid var(--border-color,#e2e8f0);
    transition: background .1s;
}
.cs-table tbody tr:hover { background: rgba(99,102,241,.06); }
.cs-table tbody td {
    padding: 9px 14px;
    color: var(--text-primary,#1c2434);
    white-space: nowrap;
}
.cs-table tfoot td {
    padding: 11px 14px;
    font-weight: 800;
    font-size: 13px;
    background: rgba(99,102,241,.08);
    border-top: 2px solid var(--border-color,#e2e8f0);
    color: var(--text-primary,#1c2434);
    position: sticky; bottom: 0;
}
.cs-table tbody td.num { text-align: right; font-variant-numeric: tabular-nums; }
.cs-table tfoot td.num { text-align: right; }

/* Row number col */
.cs-table .col-n {
    width: 40px; text-align: center;
    color: var(--text-muted,#64748b); font-size: 11px;
    background: rgba(0,0,0,.02);
    border-right: 1px solid var(--border-color,#e2e8f0);
}
.cs-table tfoot .col-n { background: rgba(99,102,241,.08); }

/* Status badge */
.st-available { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:20px; font-size:11px; font-weight:700; background:rgba(16,185,129,.12); color:#34d399; border:1px solid rgba(16,185,129,.25); }
.st-archived  { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:20px; font-size:11px; font-weight:700; background:rgba(239,68,68,.1);   color:#f87171;  border:1px solid rgba(239,68,68,.2); }

/* Print */
.cs-print-wrap { display:none; }
@media print {
    body * { visibility:hidden !important; }
    .cs-print-wrap, .cs-print-wrap * { visibility:visible !important; }
    .cs-print-wrap { position:fixed; top:0; left:0; width:100%; padding:20px; background:#fff; display:block !important; }
    .cs-print-wrap table { width:100%; border-collapse:collapse; font-size:11px; }
    .cs-print-wrap th { background:#334155!important; color:#fff!important; padding:7px 10px; text-align:left; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .cs-print-wrap td { padding:6px 10px; border-bottom:1px solid #e2e8f0; color:#1e293b; }
    .cs-print-wrap tfoot td { background:#f1f5f9!important; font-weight:700; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .cs-print-wrap .pt-title { font-size:16px; font-weight:800; margin-bottom:4px; color:#1e293b; }
    .cs-print-wrap .pt-sub { font-size:11px; color:#64748b; margin-bottom:14px; }
}
</style>

<div class="card" style="width:100%; height:100%;">
    <div class="card-header">
        <h3 class="card-title">Classroom Summary</h3>
        <div style="display:flex; gap:8px; align-items:center;">
            <a href="{{ route('reports.classroom') }}" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Classroom Report</a>
            <button class="cs-btn print" onclick="window.print()">Print</button>
            <button class="cs-btn excel" onclick="exportCsv()">Export Excel</button>
        </div>
    </div>

    {{-- Stat chips --}}
    <div style="display:flex; gap:14px; padding:14px 16px; border-bottom:1px solid var(--border-color,#e2e8f0); flex-wrap:wrap;">
        <div class="stat-card" style="flex:1; min-width:120px;">
            <div class="stat-label">Total Classrooms</div>
            <div class="stat-value">{{ $totalActive + $totalArchived }}</div>
        </div>
        <div class="stat-card" style="flex:1; min-width:120px;">
            <div class="stat-label">Available</div>
            <div class="stat-value" style="color:#34d399;">{{ $totalActive }}</div>
        </div>
        <div class="stat-card" style="flex:1; min-width:120px;">
            <div class="stat-label">Archived</div>
            <div class="stat-value" style="color:#f87171;">{{ $totalArchived }}</div>
        </div>
        <div class="stat-card" style="flex:1; min-width:120px;">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ $classrooms->sum(fn($c) => $c->enrollments->count()) }}</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="cs-toolbar">
        <div class="cs-search-wrap">
            <span style="color:var(--text-muted,#64748b); font-size:13px;"><i data-lucide="search" style="width:14px;height:14px;"></i></span>
            <input type="text" id="csSearch" placeholder="Search classroom, grade, teacher…" oninput="filterTable()">
        </div>
        <select id="csStatusFilter" class="form-control" style="width:auto; font-size:13px;" onchange="filterTable()">
            <option value="">All Status</option>
            <option value="available">Available</option>
            <option value="archived">Archived</option>
        </select>
        <span class="cs-count" id="csCount">{{ $classrooms->count() }} classrooms</span>
    </div>

    {{-- Spreadsheet table --}}
    <div class="cs-table-wrap" style="max-height:calc(100vh - 320px); overflow-y:auto;">
        <table class="cs-table" id="csTable">
            <thead>
                <tr>
                    <th class="col-n">#</th>
                    <th onclick="sortTable(1)">Classroom <span class="sort-icon">⇅</span></th>
                    <th onclick="sortTable(2)">Grade <span class="sort-icon">⇅</span></th>
                    <th onclick="sortTable(3)">Term <span class="sort-icon">⇅</span></th>
                    <th onclick="sortTable(4)">Teacher <span class="sort-icon">⇅</span></th>
                    <th onclick="sortTable(5)">Turn <span class="sort-icon">⇅</span></th>
                    <th onclick="sortTable(6)" style="text-align:right;">Capacity <span class="sort-icon">⇅</span></th>
                    <th onclick="sortTable(7)" style="text-align:right;">Students <span class="sort-icon">⇅</span></th>
                    <th onclick="sortTable(8)">Status <span class="sort-icon">⇅</span></th>
                </tr>
            </thead>
            <tbody id="csBody">
                @foreach($classrooms as $i => $cr)
                <tr data-status="{{ $cr->is_delete ? 'archived' : 'available' }}"
                    data-search="{{ strtolower($cr->name . ' ' . ($cr->grade->name??'') . ' ' . ($cr->grade?->term?->name??'') . ' ' . ($cr->teacher->name??'')) }}">
                    <td class="col-n">{{ $i + 1 }}</td>
                    <td><strong>{{ $cr->name }}</strong></td>
                    <td>{{ $cr->grade->name ?? '—' }}</td>
                    <td>{{ $cr->grade?->term?->name ?? '—' }}</td>
                    <td>{{ $cr->teacher->name ?? '—' }}</td>
                    <td>{{ $cr->turn->name ?? '—' }}</td>
                    <td class="num">{{ $cr->capacity ?? '—' }}</td>
                    <td class="num"><strong>{{ $cr->enrollments->count() }}</strong></td>
                    <td>
                        @if($cr->is_delete)
                            <span class="st-archived">● Archived</span>
                        @else
                            <span class="st-available">● Available</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="col-n"></td>
                    <td colspan="6"><strong>TOTAL</strong></td>
                    <td class="num" id="footerStudents"><strong>{{ $classrooms->sum(fn($c) => $c->enrollments->count()) }}</strong></td>
                    <td id="footerStatus">{{ $totalActive }} available · {{ $totalArchived }} archived</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Print area --}}
<div class="cs-print-wrap" id="csPrint">
    <div class="pt-title">Classroom Summary Report</div>
    <div class="pt-sub">Generated: {{ now()->format('d M Y, H:i') }} &nbsp;·&nbsp; Total: {{ $classrooms->count() }} classrooms</div>
    <table>
        <thead>
            <tr>
                <th>#</th><th>Classroom</th><th>Grade</th><th>Term</th>
                <th>Teacher</th><th>Turn</th><th>Capacity</th><th>Students</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classrooms as $i => $cr)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $cr->name }}</td>
                <td>{{ $cr->grade->name ?? '—' }}</td>
                <td>{{ $cr->grade?->term?->name ?? '—' }}</td>
                <td>{{ $cr->teacher->name ?? '—' }}</td>
                <td>{{ $cr->turn->name ?? '—' }}</td>
                <td>{{ $cr->capacity ?? '—' }}</td>
                <td>{{ $cr->enrollments->count() }}</td>
                <td>{{ $cr->is_delete ? 'Archived' : 'Available' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7"><strong>TOTAL</strong></td>
                <td><strong>{{ $classrooms->sum(fn($c) => $c->enrollments->count()) }}</strong></td>
                <td>{{ $totalActive }} avail · {{ $totalArchived }} archived</td>
            </tr>
        </tfoot>
    </table>
</div>

@push('scripts')
<script>
// ── Search + Status filter ──
function filterTable() {
    const q      = document.getElementById('csSearch').value.toLowerCase();
    const status = document.getElementById('csStatusFilter').value;
    const rows   = document.querySelectorAll('#csBody tr');
    let visible  = 0;
    rows.forEach(r => {
        const matchQ = !q || r.dataset.search.includes(q);
        const matchS = !status || r.dataset.status === status;
        const show   = matchQ && matchS;
        r.style.display = show ? '' : 'none';
        if (show) visible++;
        // renumber
        if (show) { visible; r.querySelector('.col-n').textContent = visible; }
    });
    document.getElementById('csCount').textContent = visible + ' classroom' + (visible !== 1 ? 's' : '');
}

// ── Sort ──
let sortDir = {};
function sortTable(col) {
    const tbody = document.getElementById('csBody');
    const rows  = Array.from(tbody.querySelectorAll('tr'));
    const asc   = !sortDir[col];
    sortDir = {}; sortDir[col] = asc;
    document.querySelectorAll('.cs-table thead th').forEach(th => th.classList.remove('sorted'));
    document.querySelectorAll('.cs-table thead th')[col].classList.add('sorted');
    rows.sort((a, b) => {
        const av = a.cells[col]?.innerText.trim() ?? '';
        const bv = b.cells[col]?.innerText.trim() ?? '';
        const n  = Number(av) - Number(bv);
        const cmp = isNaN(n) ? av.localeCompare(bv) : n;
        return asc ? cmp : -cmp;
    });
    rows.forEach(r => tbody.appendChild(r));
    // renumber
    let n = 0;
    rows.forEach(r => { if (r.style.display !== 'none') r.querySelector('.col-n').textContent = ++n; });
}

// ── CSV/Excel export ──
function exportCsv() {
    const rows   = Array.from(document.querySelectorAll('#csBody tr'));
    const headers = ['#','Classroom','Grade','Term','Teacher','Turn','Capacity','Students','Status'];
    const lines  = [headers.join(',')];
    rows.forEach((r, i) => {
        if (r.style.display === 'none') return;
        const cells = Array.from(r.cells).map(c => `"${c.innerText.replace(/"/g,'""').trim()}"`);
        lines.push(cells.join(','));
    });
    // Totals
    lines.push(`"","TOTAL","","","","","","{{ $classrooms->sum(fn($c) => $c->enrollments->count()) }}",""`);
    const blob = new Blob(['\uFEFF' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `classroom-summary-{{ now()->format('Ymd') }}.csv`;
    a.click();
}
</script>
@endpush
@endsection
