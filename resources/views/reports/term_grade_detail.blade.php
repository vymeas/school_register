@extends('layouts.app')
@section('title','Grade Detail — '.$grade->name)
@section('page-title','Grade Detail Report')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:11px;page-break-inside:auto}.rpt-print tr{page-break-inside:avoid}.rpt-print th{background:#1e293b!important;color:#fff!important;padding:8px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:6px 10px;border-bottom:1px solid #e2e8f0;color:#1e293b}.rpt-print h2{font-size:18px;font-weight:800;margin-bottom:4px}.rpt-print .sub{font-size:11px;color:#64748b;margin-bottom:14px}.rpt-print .rf{margin-top:14px;font-size:10px;color:#94a3b8;text-align:right}.rpt-print tr.cr-row td{background:#f1f5f9!important;font-weight:700;-webkit-print-color-adjust:exact;print-color-adjust:exact}}
.rpt-print{display:none}
.cr-card{background:var(--bg-card,#1e293b);border:1px solid var(--border-color,#2d3f55);border-radius:12px;overflow:hidden;margin-bottom:14px}
.cr-hdr{display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border-color,#2d3f55);cursor:pointer;user-select:none;background:rgba(255,255,255,.02)}
.cr-hdr:hover{background:rgba(99,102,241,.07)}
.cr-av{width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0}
.cr-tbl{width:100%;border-collapse:collapse}
.cr-tbl th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:9px 14px;border-bottom:1px solid var(--border-color,#2d3f55);background:rgba(255,255,255,.015)}
.cr-tbl td{padding:8px 14px;font-size:12px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.cr-tbl tbody tr:hover{background:rgba(99,102,241,.05)}
.cr-body{display:none}.cr-body.open{display:block}
</style>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
  <div>
    <div style="font-size:17px;font-weight:800;color:var(--text-primary,#e2e8f0);">{{ $grade->name }}</div>
    <div style="font-size:12px;color:var(--text-muted,#64748b);">{{ $grade->term?->name ?? '—' }} · {{ $grade->classrooms->count() }} classrooms</div>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="{{ route('reports.term-grade') }}" class="btn btn-secondary btn-sm">Back</a>
    <button class="btn btn-secondary btn-sm" onclick="window.print()" style="border-color:rgba(99,102,241,.4);color:#818cf8;">Print</button>
  </div>
</div>

{{-- Summary chips --}}
@php
  $totalStudents = $grade->classrooms->sum(fn($c) => $c->enrollments->count());
  $totalRevenue  = $grade->classrooms->sum(fn($c) => $c->enrollments->sum(fn($e) => $e->payments->sum('amount')));
@endphp
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">
  <div class="stat-card"><div class="stat-label">Grade</div><div class="stat-value">{{ $grade->name }}</div></div>
  <div class="stat-card"><div class="stat-label">Term</div><div class="stat-value">{{ $grade->term?->name ?? '—' }}</div></div>
  <div class="stat-card"><div class="stat-label">Total Students</div><div class="stat-value" style="color:#34d399;">{{ $totalStudents }}</div></div>
  <div class="stat-card"><div class="stat-label">Total Revenue</div><div class="stat-value" style="color:#34d399;">${{ number_format($totalRevenue,2) }}</div></div>
</div>

@forelse($grade->classrooms as $i => $cr)
@php
  $crStudents = $cr->enrollments->count();
  $crRevenue  = $cr->enrollments->sum(fn($e) => $e->payments->sum('amount'));
  $paidCount  = $cr->enrollments->filter(fn($e) => $e->payments->isNotEmpty())->count();
@endphp
<div class="cr-card">
  <div class="cr-hdr" onclick="toggleCr('cr{{ $i }}')">
    <div class="cr-av">{{ strtoupper(substr($cr->name,0,2)) }}</div>
    <div style="flex:1;min-width:0;">
      <div style="font-size:14px;font-weight:700;color:var(--text-primary,#e2e8f0);">{{ $cr->name }}</div>
      <div style="font-size:11px;color:var(--text-muted,#64748b);">{{ $cr->teacher?->name ?? 'No Teacher' }} · {{ $cr->turn?->name ?? '—' }}</div>
    </div>
    <div style="display:flex;gap:16px;flex-shrink:0;">
      <div style="text-align:center;"><div style="font-size:16px;font-weight:800;">{{ $crStudents }}</div><div style="font-size:10px;color:var(--text-muted,#64748b);text-transform:uppercase;">Students</div></div>
      <div style="text-align:center;"><div style="font-size:16px;font-weight:800;">{{ $paidCount }}</div><div style="font-size:10px;color:var(--text-muted,#64748b);text-transform:uppercase;">Paid</div></div>
      <div style="text-align:center;"><div style="font-size:16px;font-weight:800;color:#34d399;">${{ number_format($crRevenue,0) }}</div><div style="font-size:10px;color:var(--text-muted,#64748b);text-transform:uppercase;">Revenue</div></div>
    </div>
    <span id="chev-cr{{ $i }}" style="font-size:11px;color:var(--text-muted,#64748b);margin-left:10px;transition:transform .2s;">▼</span>
  </div>
  <div class="cr-body" id="cr{{ $i }}">
    @if($cr->enrollments->isEmpty())
      <div style="padding:16px;text-align:center;color:var(--text-muted,#64748b);font-size:13px;">No students enrolled.</div>
    @else
    <table class="cr-tbl">
      <thead><tr><th>#</th><th>Student</th><th>Code</th><th>Contact</th><th style="text-align:right;">Paid ($)</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($cr->enrollments as $j => $enrollment)
        @php
          $s = $enrollment->student;
          $paid = $enrollment->payments->sum('amount');
          $last = $enrollment->payments->sortByDesc('payment_date')->first();
          $until = $last?->end_study_date;
          $isActive = $until && now()->lte($until);
        @endphp
        <tr>
          <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ $j+1 }}</td>
          <td><strong>{{ $s->first_name ?? '' }} {{ $s->last_name ?? '' }}</strong></td>
          <td><span class="badge secondary" style="font-size:10px;">{{ $s->student_code ?? '—' }}</span></td>
          <td style="color:var(--text-muted,#64748b);">{{ $s->parent_phone ?? '—' }}</td>
          <td style="text-align:right;"><strong style="color:#34d399;">${{ number_format($paid,2) }}</strong></td>
          <td>
            @if($until)
              <span class="badge {{ $isActive?'active':'expired' }}">{{ $isActive?'Active':'Expired' }}</span>
            @else
              <span class="badge secondary" style="font-size:10px;">No Payment</span>
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
  <div class="empty-state"><div class="empty-icon"><i data-lucide="book-open"></i></div><h3>No classrooms in this grade.</h3></div>
@endforelse

<div class="rpt-print">
  <h2>Grade Detail — {{ $grade->name }}</h2>
  <div class="sub">Term: {{ $grade->term?->name ?? '—' }} | Generated: {{ now()->format('d M Y, H:i') }} | By: {{ auth()->user()->full_name ?? 'Admin' }}</div>
  <table>
    <thead><tr><th>#</th><th>Classroom</th><th>Teacher</th><th>Turn</th><th>Student</th><th>Code</th><th>Contact</th><th>Paid ($)</th><th>Status</th></tr></thead>
    <tbody>
      @php $rn=0; @endphp
      @foreach($grade->classrooms as $cr)
        <tr class="cr-row"><td colspan="9">{{ $cr->name }} · {{ $cr->teacher?->name??'No Teacher' }} · {{ $cr->enrollments->count() }} students · ${{ number_format($cr->enrollments->sum(fn($e)=>$e->payments->sum('amount')),2) }}</td></tr>
        @foreach($cr->enrollments as $enrollment)
          @php $rn++;$s=$enrollment->student;$paid=$enrollment->payments->sum('amount');$last=$enrollment->payments->sortByDesc('payment_date')->first();$until=$last?->end_study_date;$isActive=$until&&now()->lte($until); @endphp
          <tr><td>{{ $rn }}</td><td>{{ $cr->name }}</td><td>{{ $cr->teacher?->name??'—' }}</td><td>{{ $cr->turn?->name??'—' }}</td><td>{{ $s->first_name??'' }} {{ $s->last_name??'' }}</td><td>{{ $s->student_code??'—' }}</td><td>{{ $s->parent_phone??'—' }}</td><td>${{ number_format($paid,2) }}</td><td>{{ $until?($isActive?'Active':'Expired'):'No Payment' }}</td></tr>
        @endforeach
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="7"><strong>TOTAL</strong></td><td><strong>${{ number_format($totalRevenue,2) }}</strong></td><td><strong>{{ $totalStudents }} students</strong></td></tr></tfoot>
  </table>
  <div class="rf">School Register · Grade Detail Report · {{ now()->format('d M Y') }}</div>
</div>

@push('scripts')
<script>
function toggleCr(id){const b=document.getElementById(id);const c=document.getElementById('chev-'+id);const open=b.classList.toggle('open');c.style.transform=open?'rotate(180deg)':'none';}
</script>
@endpush
@endsection
