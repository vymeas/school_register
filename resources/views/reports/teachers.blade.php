@extends('layouts.app')
@section('title','Teacher Directory')
@section('page-title','Teacher Directory')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:11px}.rpt-print th{background:#1e293b!important;color:#fff!important;padding:8px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:6px 10px;border-bottom:1px solid #e2e8f0;color:#1e293b}.rpt-print h2{font-size:18px;font-weight:800;margin-bottom:4px}.rpt-print .sub{font-size:11px;color:#64748b;margin-bottom:14px}.rpt-print .rf{margin-top:14px;font-size:10px;color:#94a3b8;text-align:right}}
.rpt-print{display:none}
.t-tbl{width:100%;border-collapse:collapse}
.t-tbl th{background:var(--bg-secondary,#0f172a);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:10px 14px;text-align:left;border-bottom:2px solid var(--border-color,#2d3f55);cursor:pointer;user-select:none;white-space:nowrap}
.t-tbl td{padding:10px 14px;font-size:13px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.t-tbl tbody tr:hover{background:rgba(99,102,241,.06)}
.t-tbl tfoot td{padding:10px 14px;font-weight:800;background:rgba(99,102,241,.08);border-top:2px solid var(--border-color,#2d3f55)}
.t-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#818cf8);display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0}
.xbtn{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .15s;white-space:nowrap}
.xbtn.pr{background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)}.xbtn.pr:hover{background:#6366f1;color:#fff}
.xbtn.ex{background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.3)}.xbtn.ex:hover{background:#10b981;color:#fff}
.t-srch{display:flex;align-items:center;gap:8px;border:1px solid var(--border-color,#2d3f55);border-radius:8px;background:var(--bg-secondary,#0f172a);padding:6px 12px;max-width:260px}
.t-srch input{border:none;background:transparent;color:var(--text-primary,#e2e8f0);font-size:13px;outline:none;width:100%}
</style>

<div class="card" style="width:100%;height:100%;">
  <div class="card-header">
    <h3 class="card-title">👩‍🏫 Teacher Directory</h3>
    <div style="display:flex;gap:8px;align-items:center;">
      <button class="xbtn pr" onclick="window.print()">🖨️ Print</button>
      <button class="xbtn ex" onclick="exportTCsv()">📊 Export</button>
    </div>
  </div>

  <div style="padding:14px 16px 0;">
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:14px;">
      <div class="stat-card"><div class="stat-label">Total Teachers</div><div class="stat-value">{{ $teachers->count() }}</div></div>
      <div class="stat-card"><div class="stat-label">Total Classrooms</div><div class="stat-value">{{ $teachers->sum('classrooms_count') }}</div></div>
      <div class="stat-card"><div class="stat-label">Avg Classrooms/Teacher</div><div class="stat-value">{{ $teachers->count() ? round($teachers->sum('classrooms_count') / $teachers->count(), 1) : 0 }}</div></div>
    </div>
  </div>

  <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--border-color,#2d3f55);flex-wrap:wrap;">
    <div class="t-srch"><span style="color:var(--text-muted,#64748b);">🔍</span><input id="tsearch" placeholder="Search teacher, email…" oninput="filterT()"></div>
    <span id="tcnt" style="font-size:12px;color:var(--text-muted,#64748b);margin-left:auto;">{{ $teachers->count() }} teachers</span>
  </div>

  <div style="overflow-x:auto;max-height:calc(100vh - 320px);overflow-y:auto;">
    <table class="t-tbl" id="ttbl">
      <thead>
        <tr>
          <th onclick="sortT(0)">#</th>
          <th onclick="sortT(1)">Code ⇅</th>
          <th onclick="sortT(2)">Teacher ⇅</th>
          <th onclick="sortT(3)">Gender ⇅</th>
          <th onclick="sortT(4)">Phone ⇅</th>
          <th onclick="sortT(5)">Email ⇅</th>
          <th onclick="sortT(6)" style="text-align:right;">Classrooms ⇅</th>
          <th>Classes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="tbody">
        @foreach($teachers as $i => $teacher)
        <tr data-search="{{ strtolower($teacher->name.' '.($teacher->email??'').' '.($teacher->teacher_code??'')) }}">
          <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ $i+1 }}</td>
          <td><span class="badge secondary" style="font-size:10px;">{{ $teacher->teacher_code ?? '—' }}</span></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="t-avatar">{{ strtoupper(substr($teacher->name,0,2)) }}</div>
              <strong>{{ $teacher->name }}</strong>
            </div>
          </td>
          <td>{{ ucfirst($teacher->gender ?? '—') }}</td>
          <td>{{ $teacher->phone ?? '—' }}</td>
          <td style="color:var(--text-muted,#64748b);">{{ $teacher->email ?? '—' }}</td>
          <td style="text-align:right;"><strong>{{ $teacher->classrooms_count }}</strong></td>
          <td>
            @foreach($teacher->classrooms->take(3) as $cr)
              <span style="font-size:10px;padding:2px 7px;border-radius:12px;background:rgba(99,102,241,.1);color:#818cf8;margin-right:3px;">{{ $cr->name }}</span>
            @endforeach
            @if($teacher->classrooms->count() > 3)
              <span style="font-size:10px;color:var(--text-muted,#64748b);">+{{ $teacher->classrooms->count() - 3 }} more</span>
            @endif
          </td>
          <td><a href="{{ route('reports.teachers.schedule', $teacher->id) }}" class="btn btn-secondary btn-sm" style="font-size:11px;padding:3px 8px;">Schedule →</a></td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6"><strong>TOTAL</strong></td>
          <td style="text-align:right;"><strong>{{ $teachers->sum('classrooms_count') }}</strong></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<div class="rpt-print">
  <h2>👩‍🏫 Teacher Directory</h2>
  <div class="sub">Generated: {{ now()->format('d M Y, H:i') }} · By: {{ auth()->user()->full_name ?? 'Admin' }} · {{ $teachers->count() }} teachers</div>
  <table>
    <thead><tr><th>#</th><th>Code</th><th>Name</th><th>Gender</th><th>Phone</th><th>Email</th><th>Classrooms</th><th>Classes</th></tr></thead>
    <tbody>
      @foreach($teachers as $i => $teacher)
      <tr>
        <td>{{ $i+1 }}</td><td>{{ $teacher->teacher_code??'—' }}</td><td>{{ $teacher->name }}</td>
        <td>{{ ucfirst($teacher->gender??'—') }}</td><td>{{ $teacher->phone??'—' }}</td>
        <td>{{ $teacher->email??'—' }}</td><td>{{ $teacher->classrooms_count }}</td>
        <td>{{ $teacher->classrooms->pluck('name')->join(', ') }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="6"><strong>TOTAL {{ $teachers->count() }} teachers</strong></td><td>{{ $teachers->sum('classrooms_count') }}</td><td></td></tr></tfoot>
  </table>
  <div class="rf">School Register · Teacher Directory · {{ now()->format('d M Y') }}</div>
</div>

@push('scripts')
<script>
function filterT(){
  const q=document.getElementById('tsearch').value.toLowerCase();
  let n=0;
  document.querySelectorAll('#tbody tr').forEach(r=>{
    const ok=!q||r.dataset.search.includes(q);
    r.style.display=ok?'':'none';
    if(ok){n++;r.cells[0].textContent=n;}
  });
  document.getElementById('tcnt').textContent=n+' teacher'+(n!==1?'s':'');
}
let tDir={};
function sortT(c){
  const tb=document.getElementById('tbody');
  const rows=Array.from(tb.querySelectorAll('tr'));
  const asc=!tDir[c];tDir={};tDir[c]=asc;
  rows.sort((a,b)=>{const av=a.cells[c]?.innerText.trim()||'';const bv=b.cells[c]?.innerText.trim()||'';const n=Number(av)-Number(bv);return(isNaN(n)?av.localeCompare(bv):n)*(asc?1:-1);});
  rows.forEach(r=>tb.appendChild(r));
  let n=0;rows.forEach(r=>{if(r.style.display!=='none')r.cells[0].textContent=++n;});
}
function exportTCsv(){
  const hdrs=['#','Code','Name','Gender','Phone','Email','Classrooms'];
  const rows=Array.from(document.querySelectorAll('#tbody tr')).filter(r=>r.style.display!=='none');
  const lines=[hdrs.join(','),...rows.map(r=>Array.from(r.cells).slice(0,7).map(c=>`"${c.innerText.replace(/"/g,'""').trim()}"`).join(','))];
  const b=new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='teachers-{{ now()->format("Ymd") }}.csv';a.click();
}
</script>
@endpush
@endsection
