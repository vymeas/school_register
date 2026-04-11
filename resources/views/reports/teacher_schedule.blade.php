@extends('layouts.app')
@section('title','Teacher Schedule — '.$teacher->name)
@section('page-title','Teacher Schedule')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:11px}.rpt-print th{background:#1e293b!important;color:#fff!important;padding:7px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:6px 10px;border-bottom:1px solid #e2e8f0;color:#1e293b}.rpt-print h2{font-size:18px;font-weight:800;margin-bottom:4px}.rpt-print .sub{font-size:11px;color:#64748b;margin-bottom:14px}.rpt-print .rf{margin-top:14px;font-size:10px;color:#94a3b8;text-align:right}}
.rpt-print{display:none}
.ts-grid{display:grid;grid-template-columns:240px 1fr;gap:18px;align-items:start}
@media(max-width:800px){.ts-grid{grid-template-columns:1fr}}
.pf-card{background:var(--bg-card,#1e293b);border:1px solid var(--border-color,#2d3f55);border-radius:12px;padding:20px}
.ts-tbl{width:100%;border-collapse:collapse}
.ts-tbl th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:9px 14px;border-bottom:2px solid var(--border-color,#2d3f55);background:rgba(255,255,255,.015);text-align:left}
.ts-tbl td{padding:9px 14px;font-size:13px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.ts-tbl tbody tr:hover{background:rgba(99,102,241,.06)}
.ts-tbl tfoot td{background:rgba(99,102,241,.08);font-weight:800;border-top:2px solid var(--border-color,#2d3f55);padding:10px 14px}
.pf-field{margin-bottom:10px}.pf-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);margin-bottom:3px}.pf-value{font-size:13px;color:var(--text-primary,#e2e8f0)}
</style>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
  <div>
    <div style="font-size:17px;font-weight:800;color:var(--text-primary,#e2e8f0);">👩‍🏫 {{ $teacher->name }}</div>
    <div style="font-size:12px;color:var(--text-muted,#64748b);">{{ $teacher->teacher_code ?? '—' }} · {{ $teacher->classrooms->count() }} classroom(s)</div>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="{{ route('reports.teachers') }}" class="btn btn-secondary btn-sm">← Back</a>
    <button class="btn btn-secondary btn-sm" onclick="window.print()" style="border-color:rgba(99,102,241,.4);color:#818cf8;">🖨️ Print Schedule</button>
  </div>
</div>

<div class="ts-grid">
  {{-- Left: Teacher Info --}}
  <div class="pf-card">
    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#fff;margin:0 auto 14px;">
      {{ strtoupper(substr($teacher->name,0,2)) }}
    </div>
    <div style="text-align:center;font-size:16px;font-weight:800;color:var(--text-primary,#e2e8f0);margin-bottom:4px;">{{ $teacher->name }}</div>
    <div style="text-align:center;margin-bottom:16px;"><span class="badge {{ $teacher->status === 'active' ? 'active' : 'secondary' }}">{{ ucfirst($teacher->status ?? '—') }}</span></div>
    @foreach([['Code', $teacher->teacher_code??'—'],['Gender', ucfirst($teacher->gender??'—')],['Phone', $teacher->phone??'—'],['Email', $teacher->email??'—'],['Hired', $teacher->hire_date?->format('d M Y')??'—'],['Address', $teacher->address??'—']] as [$l,$v])
    <div class="pf-field"><div class="pf-label">{{ $l }}</div><div class="pf-value">{{ $v }}</div></div>
    @endforeach
    <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border-color,#2d3f55);text-align:center;">
      <div style="font-size:22px;font-weight:800;color:var(--text-primary,#e2e8f0);">{{ $teacher->classrooms->count() }}</div>
      <div style="font-size:11px;color:var(--text-muted,#64748b);text-transform:uppercase;">Classrooms</div>
    </div>
  </div>

  {{-- Right: Schedule --}}
  <div class="pf-card">
    <div style="font-size:14px;font-weight:700;color:var(--text-primary,#e2e8f0);margin-bottom:14px;">📋 Classroom Schedule</div>
    @if($teacher->classrooms->isEmpty())
      <div style="text-align:center;color:var(--text-muted,#64748b);padding:30px;">No classrooms assigned.</div>
    @else
    <table class="ts-tbl">
      <thead>
        <tr>
          <th>#</th><th>Classroom</th><th>Grade</th><th>Term</th><th>Turn</th>
          <th style="text-align:right;">Students</th>
        </tr>
      </thead>
      <tbody>
        @foreach($teacher->classrooms as $i => $cr)
        <tr>
          <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ $i+1 }}</td>
          <td><strong>{{ $cr->name }}</strong></td>
          <td>{{ $cr->grade?->name ?? '—' }}</td>
          <td>{{ $cr->grade?->term?->name ?? '—' }}</td>
          <td><span class="badge secondary" style="font-size:10px;">{{ $cr->turn?->name ?? '—' }}</span></td>
          <td style="text-align:right;"><strong>{{ $cr->enrollments->count() }}</strong></td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5"><strong>TOTAL</strong></td>
          <td style="text-align:right;"><strong>{{ $teacher->classrooms->sum(fn($c) => $c->enrollments->count()) }}</strong></td>
        </tr>
      </tfoot>
    </table>
    @endif
  </div>
</div>

<div class="rpt-print">
  <h2>👩‍🏫 Teacher Schedule — {{ $teacher->name }}</h2>
  <div class="sub">Code: {{ $teacher->teacher_code??'—' }} | Phone: {{ $teacher->phone??'—' }} | Generated: {{ now()->format('d M Y, H:i') }} | By: {{ auth()->user()->full_name??'Admin' }}</div>
  <table>
    <thead><tr><th>#</th><th>Classroom</th><th>Grade</th><th>Term</th><th>Turn</th><th>Students</th></tr></thead>
    <tbody>
      @foreach($teacher->classrooms as $i => $cr)
      <tr><td>{{ $i+1 }}</td><td>{{ $cr->name }}</td><td>{{ $cr->grade?->name??'—' }}</td><td>{{ $cr->grade?->term?->name??'—' }}</td><td>{{ $cr->turn?->name??'—' }}</td><td>{{ $cr->enrollments->count() }}</td></tr>
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="5"><strong>TOTAL</strong></td><td><strong>{{ $teacher->classrooms->sum(fn($c)=>$c->enrollments->count()) }}</strong></td></tr></tfoot>
  </table>
  <div class="rf">School Register · Teacher Schedule · {{ now()->format('d M Y') }}</div>
</div>
@endsection
