@extends('layouts.app')
@section('title','Transaction Log')
@section('page-title','Transaction Log')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:10px}.rpt-print th{background:#1e293b!important;color:#fff!important;padding:7px 8px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:5px 8px;border-bottom:1px solid #e2e8f0;color:#1e293b}.rpt-print tfoot td{background:#f1f5f9!important;font-weight:700;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print h2{font-size:16px;font-weight:800;margin-bottom:4px}.rpt-print .sub{font-size:11px;color:#64748b;margin-bottom:12px}.rpt-print .rf{margin-top:12px;font-size:10px;color:#94a3b8;text-align:right;page-break-inside:avoid}}
.rpt-print{display:none}
.tt-tbl{width:100%;border-collapse:collapse}
.tt-tbl th{background:var(--bg-secondary,#0f172a);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:10px 14px;text-align:left;border-bottom:2px solid var(--border-color,#2d3f55);white-space:nowrap}
.tt-tbl td{padding:9px 14px;font-size:13px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.tt-tbl tfoot td{background:rgba(99,102,241,.08);font-weight:800;padding:10px 14px;border-top:2px solid var(--border-color,#2d3f55)}
.tt-tbl tbody tr:hover{background:rgba(99,102,241,.06)}
.xbtn{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .15s;white-space:nowrap}
.xbtn.pr{background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)}.xbtn.pr:hover{background:#6366f1;color:#fff}
.xbtn.ex{background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.3)}.xbtn.ex:hover{background:#10b981;color:#fff}
</style>

<div class="card" style="width:100%;height:100%;">
  <div class="card-header">
    <h3 class="card-title">📋 Transaction Log</h3>
    <div style="display:flex;gap:8px;align-items:center;">
      <a href="{{ route('reports.payment.revenue') }}" class="btn btn-secondary btn-sm">← Revenue</a>
      <button class="xbtn pr" onclick="window.print()">🖨️ Print</button>
      <button class="xbtn ex" onclick="exportTTCsv()">📊 Export</button>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('reports.payment.transactions') }}">
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid var(--border-color,#2d3f55);flex-wrap:wrap;">
      <div style="display:flex;flex-direction:column;gap:3px;">
        <label style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-muted,#64748b);">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="font-size:13px;width:auto;">
      </div>
      <div style="display:flex;flex-direction:column;gap:3px;">
        <label style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-muted,#64748b);">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="font-size:13px;width:auto;">
      </div>
      <div style="display:flex;flex-direction:column;gap:3px;">
        <label style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--text-muted,#64748b);">Method</label>
        <select name="method" class="form-control" style="font-size:13px;width:auto;">
          <option value="">All Methods</option>
          @foreach(['cash','transfer','card','online'] as $m)
            <option value="{{ $m }}" {{ request('method') === $m ? 'selected' : '' }}>{{ strtoupper($m) }}</option>
          @endforeach
        </select>
      </div>
      <div style="align-self:flex-end;display:flex;gap:6px;">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="{{ route('reports.payment.transactions') }}" class="btn btn-secondary btn-sm">Clear</a>
      </div>
      <div style="margin-left:auto;font-size:12px;color:var(--text-muted,#64748b);">
        <strong style="color:#34d399;">${{ number_format($total,2) }}</strong> total · {{ $payments->total() }} records
      </div>
    </div>
  </form>

  <div style="overflow-x:auto;">
    <table class="tt-tbl" id="tttbl">
      <thead>
        <tr>
          <th>#</th><th>Date</th><th>Ref #</th><th>Student</th>
          <th>Classroom</th><th>Plan</th><th>Method</th>
          <th style="text-align:right;">Amount ($)</th><th>Valid Until</th><th>Receipt</th>
        </tr>
      </thead>
      <tbody>
        @foreach($payments as $i => $pay)
        @php
          $until = $pay->end_study_date;
          $isActive = $until && now()->lte($until);
        @endphp
        <tr>
          <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ ($payments->currentPage()-1)*$payments->perPage()+$i+1 }}</td>
          <td>{{ $pay->payment_date?->format('d M Y') }}</td>
          <td><span class="badge secondary" style="font-size:10px;">{{ $pay->reference_number ?? '—' }}</span></td>
          <td>
            <div style="font-weight:600;">{{ $pay->student?->first_name }} {{ $pay->student?->last_name }}</div>
            <div style="font-size:11px;color:var(--text-muted,#64748b);">{{ $pay->student?->student_code ?? '' }}</div>
          </td>
          <td>{{ $pay->enrollment?->classroom?->name ?? '—' }}</td>
          <td>{{ $pay->tuitionPlan?->name ?? '—' }}</td>
          <td><span class="badge secondary" style="font-size:10px;">{{ strtoupper($pay->payment_method ?? '—') }}</span></td>
          <td style="text-align:right;"><strong style="color:#34d399;">${{ number_format($pay->amount,2) }}</strong></td>
          <td>
            @if($until)
              <span style="font-size:12px;color:{{ $isActive ? '#34d399' : '#f87171' }};">{{ \Carbon\Carbon::parse($until)->format('d M Y') }}</span>
            @else —
            @endif
          </td>
          <td><a href="{{ route('reports.payment.receipt', $pay->id) }}" style="font-size:12px;color:#818cf8;">🧾</a></td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="7"><strong>PAGE TOTAL ({{ $payments->count() }} records)</strong></td>
          <td style="text-align:right;"><strong style="color:#34d399;">${{ number_format($payments->sum('amount'),2) }}</strong></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>

  {{-- Pagination --}}
  <div style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border-color,#2d3f55);">
    <span style="font-size:12px;color:var(--text-muted,#64748b);">Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }}</span>
    {{ $payments->links() }}
  </div>
</div>

<div class="rpt-print">
  <h2>📋 Transaction Log</h2>
  <div class="sub">Generated: {{ now()->format('d M Y, H:i') }} · By: {{ auth()->user()->full_name??'Admin' }} · Total: ${{ number_format($total,2) }}</div>
  <table>
    <thead><tr><th>#</th><th>Date</th><th>Ref#</th><th>Student</th><th>Classroom</th><th>Plan</th><th>Method</th><th>Amount ($)</th><th>Valid Until</th></tr></thead>
    <tbody>
      @foreach($payments as $i => $pay)
      <tr>
        <td>{{ $i+1 }}</td><td>{{ $pay->payment_date?->format('d M Y') }}</td>
        <td>{{ $pay->reference_number??'—' }}</td>
        <td>{{ $pay->student?->first_name }} {{ $pay->student?->last_name }}</td>
        <td>{{ $pay->enrollment?->classroom?->name??'—' }}</td>
        <td>{{ $pay->tuitionPlan?->name??'—' }}</td>
        <td>{{ strtoupper($pay->payment_method??'—') }}</td>
        <td>${{ number_format($pay->amount,2) }}</td>
        <td>{{ $pay->end_study_date?->format('d M Y')??'—' }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="7"><strong>TOTAL</strong></td><td><strong>${{ number_format($total,2) }}</strong></td><td></td></tr></tfoot>
  </table>
  <div class="rf">School Register · Transaction Log · {{ now()->format('d M Y') }}</div>
</div>

@push('scripts')
<script>
function exportTTCsv(){
  const hdrs=['#','Date','Ref#','Student','Code','Classroom','Plan','Method','Amount','Valid Until'];
  const rows=Array.from(document.querySelectorAll('#tttbl tbody tr'));
  const lines=[hdrs.join(','),...rows.map((r,i)=>Array.from(r.cells).slice(0,9).map(c=>`"${c.innerText.replace(/"/g,'""').trim()}"`).join(','))];
  const b=new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='transactions-{{ now()->format("Ymd") }}.csv';a.click();
}
</script>
@endpush
@endsection
