@extends('layouts.app')
@section('title','Payment Revenue Report')
@section('page-title','Payment Revenue Report')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:11px;margin-bottom:16px}.rpt-print th{background:#1e293b!important;color:#fff!important;padding:7px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:6px 10px;border-bottom:1px solid #e2e8f0;color:#1e293b}.rpt-print h2{font-size:18px;font-weight:800;margin-bottom:4px}.rpt-print .sub{font-size:11px;color:#64748b;margin-bottom:16px}.rpt-print .rf{margin-top:14px;font-size:10px;color:#94a3b8;text-align:right}}
.rpt-print{display:none}
.rev-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
@media(max-width:800px){.rev-grid{grid-template-columns:1fr}}
.rev-card{background:var(--bg-card,#1e293b);border:1px solid var(--border-color,#2d3f55);border-radius:12px;padding:18px}
.rev-card h4{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);margin-bottom:12px}
.rev-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.rev-row:last-child{border-bottom:none}
.rev-bar-wrap{width:100%;background:rgba(255,255,255,.06);border-radius:4px;height:6px;margin-top:4px}
.rev-bar{height:6px;border-radius:4px;background:linear-gradient(90deg,#6366f1,#818cf8)}
.mn-names{font-size:10px;color:var(--text-muted,#64748b);}
.xbtn{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .15s;white-space:nowrap}
.xbtn.pr{background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)}.xbtn.pr:hover{background:#6366f1;color:#fff}
.xbtn.ex{background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.3)}.xbtn.ex:hover{background:#10b981;color:#fff}
</style>

@php
  $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  $maxMonth = $monthly->max('total') ?: 1;
  $totalByPlan = $byPlan->sum('total');
  $totalByMethod = $byMethod->sum('total');
@endphp

<div class="card" style="width:100%;height:100%;">
  <div class="card-header">
    <h3 class="card-title">Payment Revenue Report — {{ $today->format('Y') }}</h3>
    <div style="display:flex;gap:8px;align-items:center;">
      <a href="{{ route('reports.payment.transactions') }}" class="btn btn-secondary btn-sm">Transactions</a>
      <a href="{{ route('reports.payment.overdue') }}" class="btn btn-secondary btn-sm" style="color:#f87171;border-color:rgba(239,68,68,.35);">Overdue</a>
      <button class="xbtn pr" onclick="window.print()">Print</button>
    </div>
  </div>

  <div class="card-body">
    {{-- Quick stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
      <div class="stat-card"><div class="stat-label">Today</div><div class="stat-value" style="color:#34d399;">${{ number_format($todayRev,2) }}</div></div>
      <div class="stat-card"><div class="stat-label">This Week</div><div class="stat-value" style="color:#818cf8;">${{ number_format($weekRev,2) }}</div></div>
      <div class="stat-card"><div class="stat-label">This Month</div><div class="stat-value" style="color:#f59e0b;">${{ number_format($monthRev,2) }}</div></div>
      <div class="stat-card"><div class="stat-label">This Year</div><div class="stat-value">${{ number_format($yearRev,2) }}</div></div>
    </div>

    <div class="rev-grid">
      {{-- Monthly bar --}}
      <div class="rev-card">
        <h4>Monthly Revenue ({{ $today->format('Y') }})</h4>
        @foreach($months as $mi => $mn)
          @php $row = $monthly->firstWhere('m', $mi+1); $val = $row?->total ?? 0; $pct = $maxMonth > 0 ? ($val/$maxMonth*100) : 0; @endphp
          <div class="rev-row">
            <span class="mn-names" style="min-width:28px;">{{ $mn }}</span>
            <div style="flex:1;margin:0 10px;">
              <div class="rev-bar-wrap"><div class="rev-bar" style="width:{{ $pct }}%;"></div></div>
            </div>
            <span style="font-size:12px;font-weight:700;color:{{ $val > 0 ? '#34d399' : 'var(--text-muted,#64748b)' }};min-width:80px;text-align:right;">${{ number_format($val,2) }}</span>
          </div>
        @endforeach
      </div>

      <div>
        {{-- By Plan --}}
        <div class="rev-card" style="margin-bottom:16px;">
          <h4>Revenue by Tuition Plan</h4>
          @forelse($byPlan as $plan)
          @php $pct = $totalByPlan > 0 ? ($plan->total/$totalByPlan*100) : 0; @endphp
          <div class="rev-row">
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text-primary,#e2e8f0);">{{ $plan->tuitionPlan?->name ?? 'Unknown' }}</div>
              <div style="font-size:10px;color:var(--text-muted,#64748b);">{{ $plan->cnt }} transactions · {{ number_format($pct,1) }}%</div>
            </div>
            <strong style="color:#34d399;">${{ number_format($plan->total,2) }}</strong>
          </div>
          @empty <div style="padding:16px;text-align:center;color:var(--text-muted,#64748b);font-size:13px;">No data</div>
          @endforelse
        </div>

        {{-- By Method --}}
        <div class="rev-card">
          <h4>Revenue by Payment Method</h4>
          @forelse($byMethod as $method)
          @php $pct = $totalByMethod > 0 ? ($method->total/$totalByMethod*100) : 0; @endphp
          <div class="rev-row">
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text-primary,#e2e8f0);">{{ strtoupper($method->payment_method ?? 'Unknown') }}</div>
              <div style="font-size:10px;color:var(--text-muted,#64748b);">{{ $method->cnt }} payments · {{ number_format($pct,1) }}%</div>
            </div>
            <strong style="color:#818cf8;">${{ number_format($method->total,2) }}</strong>
          </div>
          @empty <div style="padding:16px;text-align:center;color:var(--text-muted,#64748b);font-size:13px;">No data</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<div class="rpt-print">
  <h2>Payment Revenue Report — {{ $today->format('Y') }}</h2>
  <div class="sub">Generated: {{ now()->format('d M Y, H:i') }} · By: {{ auth()->user()->full_name??'Admin' }}</div>
  <table>
    <thead><tr><th>Period</th><th>Revenue ($)</th></tr></thead>
    <tbody>
      <tr><td>Today ({{ $today->format('d M Y') }})</td><td>${{ number_format($todayRev,2) }}</td></tr>
      <tr><td>This Week</td><td>${{ number_format($weekRev,2) }}</td></tr>
      <tr><td>This Month ({{ $today->format('F Y') }})</td><td>${{ number_format($monthRev,2) }}</td></tr>
      <tr><td>This Year ({{ $today->format('Y') }})</td><td>${{ number_format($yearRev,2) }}</td></tr>
    </tbody>
  </table>
  <table>
    <thead><tr><th>Tuition Plan</th><th>Transactions</th><th>Revenue ($)</th></tr></thead>
    <tbody>
      @foreach($byPlan as $plan)
      <tr><td>{{ $plan->tuitionPlan?->name??'Unknown' }}</td><td>{{ $plan->cnt }}</td><td>${{ number_format($plan->total,2) }}</td></tr>
      @endforeach
    </tbody>
  </table>
  <table>
    <thead><tr><th>Month</th><th>Revenue ($)</th></tr></thead>
    <tbody>
      @foreach($months as $mi => $mn)
      @php $val=$monthly->firstWhere('m',$mi+1)?->total??0; @endphp
      <tr><td>{{ $mn }} {{ $today->format('Y') }}</td><td>${{ number_format($val,2) }}</td></tr>
      @endforeach
    </tbody>
  </table>
  <div class="rf">School Register · Revenue Report · {{ now()->format('d M Y') }}</div>
</div>
@endsection
