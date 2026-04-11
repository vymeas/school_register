@extends('layouts.app')
@section('title','Student Summary Report')
@section('page-title','Student Summary Report')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:11px;page-break-inside:auto}.rpt-print tr{page-break-inside:avoid}.rpt-print th{background:#1e293b!important;color:#fff!important;padding:8px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:6px 10px;border-bottom:1px solid #e2e8f0;color:#1e293b}.rpt-print .rh{font-size:18px;font-weight:800;margin-bottom:4px}.rpt-print .rs{font-size:11px;color:#64748b;margin-bottom:14px}.rpt-print .rf{margin-top:14px;font-size:10px;color:#94a3b8;text-align:right}}
.rpt-print{display:none}
.sgrid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
@media(max-width:800px){.sgrid{grid-template-columns:1fr 1fr}}
.stbl{width:100%;border-collapse:collapse}
.stbl th{background:var(--bg-secondary,#0f172a);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:10px 14px;text-align:left;border-bottom:2px solid var(--border-color,#2d3f55);cursor:pointer;user-select:none;white-space:nowrap}
.stbl th:hover{color:var(--text-primary,#e2e8f0)}
.stbl td{padding:9px 14px;font-size:13px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.stbl tbody tr:hover{background:rgba(99,102,241,.06)}
.stbl tfoot td{padding:10px 14px;font-weight:800;background:rgba(99,102,241,.08);border-top:2px solid var(--border-color,#2d3f55)}
.s-search{display:flex;align-items:center;gap:8px;border:1px solid var(--border-color,#2d3f55);border-radius:8px;background:var(--bg-secondary,#0f172a);padding:6px 12px;max-width:280px}
.s-search input{border:none;background:transparent;color:var(--text-primary,#e2e8f0);font-size:13px;outline:none;width:100%}
.stbr{display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid var(--border-color,#2d3f55);flex-wrap:wrap}
.xbtn{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .15s;white-space:nowrap}
.xbtn.pr{background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)} .xbtn.pr:hover{background:#6366f1;color:#fff}
.xbtn.ex{background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.3)} .xbtn.ex:hover{background:#10b981;color:#fff}
</style>

<div class="card" style="width:100%;height:100%;">
  <div class="card-header">
    <h3 class="card-title">🎓 Student Summary Report</h3>
    <div style="display:flex;gap:8px;align-items:center;">
      <button class="xbtn pr" onclick="window.print()">🖨️ Print</button>
      <button class="xbtn ex" onclick="exportCsvStudents()">📊 Export</button>
    </div>
  </div>

  @php
    $total   = $students->count();
    $active  = $students->where('study_status','studying')->count();
    $dropped = $students->where('study_status','dropped')->count();
    $other   = $total - $active - $dropped;
  @endphp

  <div style="padding:14px 16px;border-bottom:1px solid var(--border-color,#2d3f55);">
    <div class="sgrid">
      <div class="stat-card"><div class="stat-label">Total Students</div><div class="stat-value">{{ $total }}</div></div>
      <div class="stat-card"><div class="stat-label">Studying</div><div class="stat-value" style="color:#34d399;">{{ $active }}</div></div>
      <div class="stat-card"><div class="stat-label">Dropped</div><div class="stat-value" style="color:#f87171;">{{ $dropped }}</div></div>
      <div class="stat-card"><div class="stat-label">By Grade Groups</div><div class="stat-value">{{ $byGrade->count() }}</div></div>
    </div>

    {{-- By Grade breakdown --}}
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
      @foreach($byGrade as $grade => $items)
        <span style="font-size:12px;padding:3px 10px;border-radius:20px;background:rgba(99,102,241,.12);color:#818cf8;border:1px solid rgba(99,102,241,.25);">
          📚 {{ $grade }}: <strong>{{ $items->count() }}</strong>
        </span>
      @endforeach
    </div>
  </div>

  {{-- Toolbar --}}
  <div class="stbr">
    <div class="s-search"><span style="color:var(--text-muted,#64748b);">🔍</span><input id="ssearch" placeholder="Search…" oninput="filterStudents()"></div>
    <select id="sstatusf" class="form-control" style="width:auto;font-size:13px;" onchange="filterStudents()">
      <option value="">All Status</option>
      <option value="studying">Studying</option>
      <option value="dropped">Dropped</option>
    </select>
    <span id="scnt" style="font-size:12px;color:var(--text-muted,#64748b);margin-left:auto;">{{ $total }} students</span>
  </div>

  <div style="overflow-x:auto;max-height:calc(100vh - 340px);overflow-y:auto;">
    <table class="stbl" id="stbl">
      <thead>
        <tr>
          <th onclick="sortS(0)">#</th>
          <th onclick="sortS(1)">Code ⇅</th>
          <th onclick="sortS(2)">Name ⇅</th>
          <th onclick="sortS(3)">Gender ⇅</th>
          <th onclick="sortS(4)">Classroom ⇅</th>
          <th onclick="sortS(5)">Grade ⇅</th>
          <th onclick="sortS(6)" style="text-align:right;">Payments ⇅</th>
          <th onclick="sortS(7)">Study Status ⇅</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="sbody">
        @foreach($students as $i => $s)
          @php
            $enrollment = $s->enrollments->first();
            $payCount   = $enrollment?->payments->count() ?? 0;
          @endphp
          <tr data-search="{{ strtolower($s->first_name.' '.$s->last_name.' '.($s->student_code??'')) }}"
              data-status="{{ $s->study_status }}">
            <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ $i+1 }}</td>
            <td><span class="badge secondary" style="font-size:10px;">{{ $s->student_code ?? '—' }}</span></td>
            <td><strong>{{ $s->first_name }} {{ $s->last_name }}</strong></td>
            <td>{{ ucfirst($s->gender ?? '—') }}</td>
            <td>{{ $enrollment?->classroom?->name ?? '—' }}</td>
            <td>{{ $enrollment?->grade?->name ?? '—' }}</td>
            <td style="text-align:right;">{{ $payCount }}</td>
            <td>
              @if($s->study_status === 'studying')
                <span class="badge active">Studying</span>
              @elseif($s->study_status === 'dropped')
                <span class="badge expired">Dropped</span>
              @else
                <span class="badge secondary">{{ $s->study_status ?? '—' }}</span>
              @endif
            </td>
            <td>
              <a href="{{ route('reports.students.profile', $s->id) }}" class="btn btn-secondary btn-sm" style="font-size:11px;padding:3px 8px;">Profile</a>
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6"><strong>TOTAL</strong></td>
          <td style="text-align:right;"><strong>{{ $students->sum(fn($s) => $s->enrollments->sum(fn($e) => $e->payments->count())) }}</strong></td>
          <td colspan="2"><strong>{{ $active }} studying · {{ $dropped }} dropped</strong></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

{{-- Print area --}}
<div class="rpt-print" id="rptPrint">
  <div class="rh">🎓 Student Summary Report</div>
  <div class="rs">Generated: {{ now()->format('d M Y, H:i') }} by {{ auth()->user()->full_name ?? 'Admin' }} · Total: {{ $total }} students</div>
  <table>
    <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Gender</th><th>Classroom</th><th>Grade</th><th>Payments</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($students as $i => $s)
        @php $enrollment = $s->enrollments->first(); @endphp
        <tr>
          <td>{{ $i+1 }}</td><td>{{ $s->student_code ?? '—' }}</td>
          <td>{{ $s->first_name }} {{ $s->last_name }}</td>
          <td>{{ ucfirst($s->gender ?? '—') }}</td>
          <td>{{ $enrollment?->classroom?->name ?? '—' }}</td>
          <td>{{ $enrollment?->grade?->name ?? '—' }}</td>
          <td>{{ $enrollment?->payments->count() ?? 0 }}</td>
          <td>{{ ucfirst($s->study_status ?? '—') }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="6"><strong>TOTAL: {{ $total }} students</strong></td><td><strong>{{ $students->sum(fn($s)=>$s->enrollments->sum(fn($e)=>$e->payments->count())) }}</strong></td><td><strong>{{ $active }} studying · {{ $dropped }} dropped</strong></td></tr></tfoot>
  </table>
  <div class="rf">School Register · Student Summary · {{ now()->format('d M Y') }} · Printed by: {{ auth()->user()->full_name ?? 'Admin' }}</div>
</div>

@push('scripts')
<script>
function filterStudents(){
  const q=document.getElementById('ssearch').value.toLowerCase();
  const st=document.getElementById('sstatusf').value;
  let n=0;
  document.querySelectorAll('#sbody tr').forEach((r,i)=>{
    const ok=(!q||r.dataset.search.includes(q))&&(!st||r.dataset.status===st);
    r.style.display=ok?'':'none';
    if(ok){n++;r.cells[0].textContent=n;}
  });
  document.getElementById('scnt').textContent=n+' student'+(n!==1?'s':'');
}
let sDir={};
function sortS(c){
  const tb=document.getElementById('sbody');
  const rows=Array.from(tb.querySelectorAll('tr'));
  const asc=!sDir[c];sDir={};sDir[c]=asc;
  rows.sort((a,b)=>{const av=a.cells[c]?.innerText.trim()||'';const bv=b.cells[c]?.innerText.trim()||'';const n=Number(av)-Number(bv);return(isNaN(n)?av.localeCompare(bv):n)*(asc?1:-1);});
  rows.forEach(r=>tb.appendChild(r));
  let n=0;rows.forEach(r=>{if(r.style.display!=='none')r.cells[0].textContent=++n;});
}
function exportCsvStudents(){
  const hdrs=['#','Code','Name','Gender','Classroom','Grade','Payments','Status'];
  const rows=Array.from(document.querySelectorAll('#sbody tr')).filter(r=>r.style.display!=='none');
  const lines=[hdrs.join(','),...rows.map(r=>Array.from(r.cells).slice(0,8).map(c=>`"${c.innerText.replace(/"/g,'""').trim()}"`).join(','))];
  const b=new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='student-summary-{{ now()->format("Ymd") }}.csv';a.click();
}
</script>
@endpush
@endsection
