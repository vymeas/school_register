@extends('layouts.app')
@section('title','Term & Grade Report')
@section('page-title','Term & Grade Report')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:11px;page-break-inside:auto}.rpt-print tr{page-break-inside:avoid}.rpt-print th{background:#1e293b!important;color:#fff!important;padding:8px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:6px 10px;border-bottom:1px solid #e2e8f0;color:#1e293b}.rpt-print h2{font-size:18px;font-weight:800;margin-bottom:4px}.rpt-print .sub{font-size:11px;color:#64748b;margin-bottom:14px}.rpt-print .rf{margin-top:14px;font-size:10px;color:#94a3b8;text-align:right}.rpt-print tr.term-row td{background:#1e293b!important;color:#fff!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}}
.rpt-print{display:none}
.tg-card{background:var(--bg-card,#1e293b);border:1px solid var(--border-color,#2d3f55);border-radius:12px;overflow:hidden;margin-bottom:16px}
.tg-term-hdr{padding:12px 18px;background:linear-gradient(135deg,rgba(99,102,241,.2),rgba(139,92,246,.1));border-bottom:1px solid var(--border-color,#2d3f55);display:flex;align-items:center;justify-content:space-between}
.tg-term-name{font-size:15px;font-weight:800;color:var(--text-primary,#e2e8f0)}
.tg-grade-tbl{width:100%;border-collapse:collapse}
.tg-grade-tbl th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:9px 16px;border-bottom:1px solid var(--border-color,#2d3f55);background:rgba(255,255,255,.015);text-align:left}
.tg-grade-tbl td{padding:10px 16px;font-size:13px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.tg-grade-tbl tbody tr:hover{background:rgba(99,102,241,.06)}
.tg-grade-tbl td.num{text-align:right}
.tg-grade-tbl tfoot td{background:rgba(99,102,241,.08);font-weight:800;border-top:2px solid var(--border-color,#2d3f55);padding:10px 16px}
.xbtn{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .15s;white-space:nowrap}
.xbtn.pr{background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)}.xbtn.pr:hover{background:#6366f1;color:#fff}
.xbtn.ex{background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.3)}.xbtn.ex:hover{background:#10b981;color:#fff}
</style>

<div class="card" style="width:100%;height:100%;">
  <div class="card-header">
    <h3 class="card-title">📚 Term &amp; Grade Report</h3>
    <div style="display:flex;gap:8px;align-items:center;">
      <button class="xbtn pr" onclick="window.print()">🖨️ Print</button>
      <button class="xbtn ex" onclick="exportTgCsv()">📊 Export</button>
    </div>
  </div>

  @php $grandClassrooms=0; $grandStudents=0; $grandRevenue=0; @endphp

  <div class="card-body">
    @forelse($terms as $term)
    @php
      $termClassrooms = $term->grades->sum(fn($g) => $g->classrooms->count());
      $termStudents   = $term->grades->sum(fn($g) => $g->classrooms->sum(fn($c) => $c->enrollments->count()));
      $grandClassrooms += $termClassrooms; $grandStudents += $termStudents;
    @endphp
    <div class="tg-card">
      <div class="tg-term-hdr">
        <div>
          <div class="tg-term-name">📅 {{ $term->name }}</div>
          <div style="font-size:11px;color:var(--text-muted,#94a3b8);margin-top:3px;">
            {{ $term->start_date?->format('d M Y') ?? '—' }} – {{ $term->end_date?->format('d M Y') ?? '—' }}
            @if($term->status === 'active') <span class="badge active" style="margin-left:6px;font-size:9px;">Active</span>@endif
          </div>
        </div>
        <div style="display:flex;gap:20px;">
          <div style="text-align:center;"><div style="font-size:18px;font-weight:800;color:var(--text-primary,#e2e8f0);">{{ $term->grades->count() }}</div><div style="font-size:10px;color:var(--text-muted,#64748b);text-transform:uppercase;">Grades</div></div>
          <div style="text-align:center;"><div style="font-size:18px;font-weight:800;color:var(--text-primary,#e2e8f0);">{{ $termClassrooms }}</div><div style="font-size:10px;color:var(--text-muted,#64748b);text-transform:uppercase;">Classrooms</div></div>
          <div style="text-align:center;"><div style="font-size:18px;font-weight:800;color:#34d399;">{{ $termStudents }}</div><div style="font-size:10px;color:var(--text-muted,#64748b);text-transform:uppercase;">Students</div></div>
        </div>
      </div>
      <table class="tg-grade-tbl">
        <thead><tr><th>#</th><th>Grade</th><th class="num">Classrooms</th><th class="num">Students</th><th class="num">Revenue ($)</th><th>Action</th></tr></thead>
        <tbody>
          @foreach($term->grades as $gi => $grade)
          @php
            $gClass = $grade->classrooms->count();
            $gStudents = $grade->classrooms->sum(fn($c) => $c->enrollments->count());
            $gRevenue  = $grade->classrooms->sum(fn($c) => $c->enrollments->sum(fn($e) => $e->payments->sum('amount') ?? 0));
            $grandRevenue += $gRevenue;
          @endphp
          <tr>
            <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ $gi+1 }}</td>
            <td><strong>{{ $grade->name }}</strong></td>
            <td class="num">{{ $gClass }}</td>
            <td class="num"><strong>{{ $gStudents }}</strong></td>
            <td class="num" style="color:#34d399;"><strong>${{ number_format($gRevenue,2) }}</strong></td>
            <td><a href="{{ route('reports.term-grade.detail', $grade->id) }}" class="btn btn-secondary btn-sm" style="font-size:11px;padding:3px 8px;">Detail →</a></td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"><strong>Subtotal — {{ $term->name }}</strong></td>
            <td class="num">{{ $termClassrooms }}</td>
            <td class="num">{{ $termStudents }}</td>
            <td class="num" style="color:#34d399;">${{ number_format($term->grades->sum(fn($g) => $g->classrooms->sum(fn($c) => $c->enrollments->sum(fn($e) => $e->payments->sum('amount') ?? 0))),2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
    @empty
      <div class="empty-state"><div class="empty-icon">📚</div><h3>No terms found</h3></div>
    @endforelse

    {{-- Grand Total --}}
    <div style="background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.3);border-radius:10px;padding:14px 20px;display:flex;gap:30px;align-items:center;">
      <span style="font-weight:800;font-size:14px;color:var(--text-primary,#e2e8f0);">🏆 GRAND TOTAL</span>
      <span>Classrooms: <strong>{{ $grandClassrooms }}</strong></span>
      <span>Students: <strong style="color:#34d399;">{{ $grandStudents }}</strong></span>
      <span>Revenue: <strong style="color:#34d399;">${{ number_format($grandRevenue,2) }}</strong></span>
    </div>
  </div>
</div>

{{-- Print area --}}
<div class="rpt-print">
  <h2>📚 Term &amp; Grade Summary Report</h2>
  <div class="sub">Generated: {{ now()->format('d M Y, H:i') }} · By: {{ auth()->user()->full_name ?? 'Admin' }}</div>
  <table>
    <thead><tr><th>Term</th><th>Grade</th><th>Classrooms</th><th>Students</th><th>Revenue ($)</th></tr></thead>
    <tbody>
      @foreach($terms as $term)
        <tr class="term-row"><td colspan="5">📅 {{ $term->name }} ({{ $term->start_date?->format('d M Y') }} – {{ $term->end_date?->format('d M Y') }})</td></tr>
        @foreach($term->grades as $grade)
        @php $r=$grade->classrooms->sum(fn($c)=>$c->enrollments->sum(fn($e)=>$e->payments->sum('amount')??0)); @endphp
        <tr><td></td><td>{{ $grade->name }}</td><td>{{ $grade->classrooms->count() }}</td><td>{{ $grade->classrooms->sum(fn($c)=>$c->enrollments->count()) }}</td><td>${{ number_format($r,2) }}</td></tr>
        @endforeach
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="2"><strong>TOTAL</strong></td><td>{{ $grandClassrooms }}</td><td>{{ $grandStudents }}</td><td>${{ number_format($grandRevenue,2) }}</td></tr></tfoot>
  </table>
  <div class="rf">School Register · Term/Grade Report · {{ now()->format('d M Y') }}</div>
</div>

@push('scripts')
<script>
function exportTgCsv(){
  const hdrs=['Term','Grade','Classrooms','Students','Revenue'];
  const lines=[hdrs.join(',')];
  @foreach($terms as $term)
    @foreach($term->grades as $grade)
    @php $r=$grade->classrooms->sum(fn($c)=>$c->enrollments->sum(fn($e)=>$e->payments->sum('amount')??0)); @endphp
    lines.push(['"{{ addslashes($term->name) }}"','"{{ addslashes($grade->name) }}"','{{ $grade->classrooms->count() }}','{{ $grade->classrooms->sum(fn($c)=>$c->enrollments->count()) }}','{{ number_format($r,2) }}'].join(','));
    @endforeach
  @endforeach
  const b=new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='term-grade-{{ now()->format("Ymd") }}.csv';a.click();
}
</script>
@endpush
@endsection
