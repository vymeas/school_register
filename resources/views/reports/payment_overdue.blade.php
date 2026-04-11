@extends('layouts.app')
@section('title','Overdue / Debt Report')
@section('page-title','Overdue & Debt Report')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.rpt-print,.rpt-print *{visibility:visible!important}.rpt-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important}.rpt-print table{width:100%;border-collapse:collapse;font-size:11px;page-break-inside:auto}.rpt-print tr{page-break-inside:avoid}.rpt-print th{background:#b91c1c!important;color:#fff!important;padding:7px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print td{padding:6px 10px;border-bottom:1px solid #fecaca;color:#1e293b}.rpt-print tfoot td{background:#fee2e2!important;font-weight:700;-webkit-print-color-adjust:exact;print-color-adjust:exact}.rpt-print h2{font-size:18px;font-weight:800;color:#991b1b;margin-bottom:4px}.rpt-print .sub{font-size:11px;color:#64748b;margin-bottom:14px}.rpt-print .rf{margin-top:14px;font-size:10px;color:#94a3b8;text-align:right}}
.rpt-print{display:none}
.od-tbl{width:100%;border-collapse:collapse}
.od-tbl th{background:var(--bg-secondary,#0f172a);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:10px 14px;text-align:left;border-bottom:2px solid var(--border-color,#2d3f55);white-space:nowrap}
.od-tbl td{padding:9px 14px;font-size:13px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.od-tbl tbody tr:hover{background:rgba(239,68,68,.05)}
.od-tbl tfoot td{background:rgba(239,68,68,.08);font-weight:800;padding:10px 14px;border-top:2px solid rgba(239,68,68,.3)}
.days-chip{display:inline-flex;align-items:center;gap:3px;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700}
.days-critical{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3)}
.days-warn{background:rgba(251,191,36,.12);color:#f59e0b;border:1px solid rgba(251,191,36,.3)}
.days-none{background:rgba(100,116,139,.1);color:#94a3b8;border:1px solid rgba(100,116,139,.2)}
.batch-bar{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:10px 16px;margin-bottom:14px;display:none;align-items:center;gap:12px;flex-wrap:wrap}
.batch-bar.active{display:flex}
.xbtn{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .15s;white-space:nowrap}
.xbtn.pr{background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3)}.xbtn.pr:hover{background:#6366f1;color:#fff}
.xbtn.ex{background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.3)}.xbtn.ex:hover{background:#10b981;color:#fff}
.xbtn.danger{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3)}.xbtn.danger:hover{background:#ef4444;color:#fff}
</style>

<div class="card" style="width:100%;height:100%;">
  <div class="card-header">
    <h3 class="card-title">⚠️ Overdue &amp; Debt Report</h3>
    <div style="display:flex;gap:8px;align-items:center;">
      <a href="{{ route('reports.payment.revenue') }}" class="btn btn-secondary btn-sm">← Revenue</a>
      <button class="xbtn pr" onclick="window.print()">🖨️ Print All</button>
      <button class="xbtn danger" id="batchPrintBtn" onclick="batchPrint()" style="display:none;">🖨️ Print Selected (<span id="batchCnt">0</span>)</button>
      <button class="xbtn ex" onclick="exportOdCsv()">📊 Export</button>
    </div>
  </div>

  @php
    $total = $enrollments->count();
    $neverPaid = $enrollments->filter(fn($e) => !$e->lastPayment)->count();
    $expired   = $total - $neverPaid;
  @endphp

  <div style="padding:14px 16px;border-bottom:1px solid var(--border-color,#2d3f55);">
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:12px;">
      <div class="stat-card"><div class="stat-label">Total Overdue</div><div class="stat-value" style="color:#f87171;">{{ $total }}</div></div>
      <div class="stat-card"><div class="stat-label">Expired Payment</div><div class="stat-value" style="color:#f59e0b;">{{ $expired }}</div></div>
      <div class="stat-card"><div class="stat-label">Never Paid</div><div class="stat-value" style="color:#94a3b8;">{{ $neverPaid }}</div></div>
    </div>
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:10px 14px;font-size:13px;color:var(--text-primary,#e2e8f0);">
      ⚠️ <strong>{{ $total }}</strong> student{{ $total != 1 ? 's' : '' }} require immediate payment follow-up.
      Select rows to batch print reminder notices.
    </div>
  </div>

  {{-- Batch action bar --}}
  <div class="batch-bar" id="batchBar">
    <span style="font-size:13px;color:var(--text-primary,#e2e8f0);"><strong id="batchCnt2">0</strong> selected</span>
    <button class="xbtn danger" onclick="batchPrint()">🖨️ Print Notices</button>
    <button class="xbtn ex" onclick="exportSelected()">📊 Export Selected</button>
    <button onclick="clearSelection()" style="background:transparent;border:none;color:var(--text-muted,#64748b);cursor:pointer;font-size:12px;">✕ Clear</button>
  </div>

  <div style="overflow-x:auto;max-height:calc(100vh - 360px);overflow-y:auto;">
    <table class="od-tbl" id="odtbl">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll" onchange="toggleAll(this)" style="cursor:pointer;"></th>
          <th>#</th><th>Student</th><th>Code</th><th>Contact</th>
          <th>Classroom</th><th>Grade</th>
          <th>Last Payment</th><th>Paid Until</th><th>Days Overdue</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($enrollments as $i => $e)
        @php
          $s = $e->student;
          $until = $e->lastPayment?->end_study_date;
          $days = $e->daysOverdue;
          $chipClass = !$until ? 'days-none' : ($days > 30 ? 'days-critical' : 'days-warn');
        @endphp
        <tr data-id="{{ $e->id }}" data-name="{{ $s->first_name }} {{ $s->last_name }}" data-code="{{ $s->student_code }}" data-phone="{{ $s->parent_phone }}" data-classroom="{{ $e->classroom?->name }}" data-days="{{ $days ?? 0 }}">
          <td><input type="checkbox" class="od-check" onchange="updateBatch()" style="cursor:pointer;"></td>
          <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ $i+1 }}</td>
          <td>
            <div style="font-weight:600;">{{ $s->first_name ?? '' }} {{ $s->last_name ?? '' }}</div>
          </td>
          <td><span class="badge secondary" style="font-size:10px;">{{ $s->student_code ?? '—' }}</span></td>
          <td style="color:var(--text-muted,#64748b);">{{ $s->parent_phone ?? '—' }}</td>
          <td>{{ $e->classroom?->name ?? '—' }}</td>
          <td>{{ $e->grade?->name ?? '—' }}</td>
          <td>{{ $e->lastPayment?->payment_date?->format('d M Y') ?? '—' }}</td>
          <td>
            @if($until)
              <span style="color:#f87171;font-size:12px;">{{ \Carbon\Carbon::parse($until)->format('d M Y') }}</span>
            @else
              <span style="color:#94a3b8;font-size:12px;">Never</span>
            @endif
          </td>
          <td>
            @if(!$until)
              <span class="days-chip days-none">⊘ No Payment</span>
            @elseif($days > 30)
              <span class="days-chip days-critical">↑ {{ $days }} days</span>
            @else
              <span class="days-chip days-warn">{{ $days }} days</span>
            @endif
          </td>
          <td>
            <a href="{{ route('payments.create', ['student_id' => $s->id]) }}" class="btn btn-primary btn-sm" style="font-size:11px;padding:3px 8px;">💳 Pay</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="11"><div style="padding:40px;text-align:center;color:var(--text-muted,#64748b);">✅ No overdue students found!</div></td></tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2"></td>
          <td colspan="8"><strong>TOTAL OVERDUE: {{ $total }} students</strong></td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

{{-- Print area (all) --}}
<div class="rpt-print" id="odPrintAll" style="display:none;">
  <h2>⚠️ Overdue &amp; Debt Report</h2>
  <div class="sub">Generated: {{ now()->format('d M Y, H:i') }} · By: {{ auth()->user()->full_name??'Admin' }} · {{ $total }} overdue students</div>
  <table>
    <thead><tr><th>#</th><th>Student</th><th>Code</th><th>Contact</th><th>Classroom</th><th>Grade</th><th>Last Payment</th><th>Paid Until</th><th>Days Overdue</th></tr></thead>
    <tbody>
      @foreach($enrollments as $i => $e)
      @php $s=$e->student;$until=$e->lastPayment?->end_study_date; @endphp
      <tr>
        <td>{{ $i+1 }}</td><td>{{ $s->first_name??'' }} {{ $s->last_name??'' }}</td>
        <td>{{ $s->student_code??'—' }}</td><td>{{ $s->parent_phone??'—' }}</td>
        <td>{{ $e->classroom?->name??'—' }}</td><td>{{ $e->grade?->name??'—' }}</td>
        <td>{{ $e->lastPayment?->payment_date?->format('d M Y')??'—' }}</td>
        <td>{{ $until ? \Carbon\Carbon::parse($until)->format('d M Y') : 'Never' }}</td>
        <td>{{ $e->daysOverdue ? $e->daysOverdue.' days' : 'No Payment' }}</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="2"><strong>TOTAL</strong></td><td colspan="7"><strong>{{ $total }} overdue students</strong></td></tr></tfoot>
  </table>
  <div class="rf">School Register · Overdue Report · {{ now()->format('d M Y') }}</div>
</div>

{{-- Batch print area --}}
<div class="rpt-print" id="odPrintBatch" style="display:none;">
  <h2>⚠️ Overdue Payment Reminder Notices</h2>
  <div class="sub">Generated: {{ now()->format('d M Y, H:i') }} · By: {{ auth()->user()->full_name??'Admin' }}</div>
  <div id="batchNotices"></div>
  <div class="rf">School Register · Overdue Notices · {{ now()->format('d M Y') }}</div>
</div>

@push('scripts')
<script>
function updateBatch(){
  const n=document.querySelectorAll('.od-check:checked').length;
  document.getElementById('batchCnt').textContent=n;
  document.getElementById('batchCnt2').textContent=n;
  document.getElementById('batchBar').classList.toggle('active',n>0);
  document.getElementById('batchPrintBtn').style.display=n>0?'inline-flex':'none';
}
function toggleAll(cb){
  document.querySelectorAll('.od-check').forEach(c=>c.checked=cb.checked);
  updateBatch();
}
function clearSelection(){
  document.querySelectorAll('.od-check').forEach(c=>c.checked=false);
  document.getElementById('selectAll').checked=false;
  updateBatch();
}
function batchPrint(){
  const rows=Array.from(document.querySelectorAll('#odtbl tbody tr')).filter(r=>r.querySelector('.od-check:checked'));
  if(!rows.length){alert('Please select at least one student.');return;}
  const notices=rows.map(r=>`
    <div style="border:1px solid #e2e8f0;border-radius:6px;padding:16px;margin-bottom:20px;page-break-inside:avoid;">
      <div style="font-size:14px;font-weight:800;margin-bottom:8px;">⚠️ PAYMENT REMINDER NOTICE</div>
      <div style="font-size:11px;color:#64748b;margin-bottom:10px;">Date: {{ now()->format('d M Y') }}</div>
      <table style="width:100%;border-collapse:collapse;font-size:11px;">
        <tr><td style="padding:4px 0;color:#64748b;width:140px;">Student Name</td><td><strong>${r.dataset.name}</strong></td></tr>
        <tr><td style="padding:4px 0;color:#64748b;">Student Code</td><td>${r.dataset.code}</td></tr>
        <tr><td style="padding:4px 0;color:#64748b;">Classroom</td><td>${r.dataset.classroom}</td></tr>
        <tr><td style="padding:4px 0;color:#64748b;">Parent Contact</td><td>${r.dataset.phone}</td></tr>
        <tr><td style="padding:4px 0;color:#64748b;">Days Overdue</td><td style="color:#b91c1c;font-weight:700;">${r.dataset.days > 0 ? r.dataset.days+' days' : 'No payment recorded'}</td></tr>
      </table>
      <div style="margin-top:10px;font-size:11px;color:#475569;">Please settle your outstanding tuition fee as soon as possible. Thank you.</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:24px;">
        <div style="text-align:center;"><div style="border-top:1px solid #94a3b8;padding-top:6px;font-size:10px;color:#64748b;">Cashier Signature</div></div>
        <div style="text-align:center;"><div style="border-top:1px solid #94a3b8;padding-top:6px;font-size:10px;color:#64748b;">Student / Parent Signature</div></div>
      </div>
    </div>
  `).join('');
  document.getElementById('batchNotices').innerHTML=notices;
  document.getElementById('odPrintAll').style.display='none';
  document.getElementById('odPrintBatch').style.display='block';
  window.print();
  setTimeout(()=>{document.getElementById('odPrintBatch').style.display='none';},1500);
}
function exportOdCsv(){
  const hdrs=['#','Student','Code','Contact','Classroom','Grade','Last Payment','Paid Until','Days Overdue'];
  const rows=Array.from(document.querySelectorAll('#odtbl tbody tr'));
  const lines=[hdrs.join(','),...rows.map((r,i)=>Array.from(r.cells).slice(1,10).map(c=>`"${c.innerText.replace(/"/g,'""').trim()}"`).join(','))];
  lines.push(`"","TOTAL ${rows.length} overdue","","","","","","",""`);
  const b=new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='overdue-report-{{ now()->format("Ymd") }}.csv';a.click();
}
function exportSelected(){
  const hdrs=['Student','Code','Contact','Classroom','Days Overdue'];
  const rows=Array.from(document.querySelectorAll('#odtbl tbody tr')).filter(r=>r.querySelector('.od-check:checked'));
  const lines=[hdrs.join(','),...rows.map(r=>[`"${r.dataset.name}"`,`"${r.dataset.code}"`,`"${r.dataset.phone}"`,`"${r.dataset.classroom}"`,`"${r.dataset.days}"`].join(','))];
  const b=new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='selected-overdue-{{ now()->format("Ymd") }}.csv';a.click();
}
// Print all
document.querySelector('[onclick="window.print()"]')?.removeAttribute('onclick');
document.querySelectorAll('.xbtn.pr')[0]?.addEventListener('click',()=>{
  document.getElementById('odPrintAll').style.display='block';
  document.getElementById('odPrintBatch').style.display='none';
  window.print();
  setTimeout(()=>document.getElementById('odPrintAll').style.display='none',1500);
});
</script>
@endpush
@endsection
