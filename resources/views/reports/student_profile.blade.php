@extends('layouts.app')
@section('title','Student Profile — '.$student->first_name.' '.$student->last_name)
@section('page-title','Student Profile')
@section('content')
<style>
@media print{body *{visibility:hidden!important}.prof-print,.prof-print *{visibility:visible!important}.prof-print{position:fixed;top:0;left:0;width:100%;padding:24px;background:#fff;display:block!important;font-size:12px;color:#1e293b}.prof-print h2{font-size:18px;font-weight:800;margin-bottom:4px}.prof-print .sub{font-size:11px;color:#64748b;margin-bottom:16px}.prof-print table{width:100%;border-collapse:collapse;margin-bottom:16px}.prof-print th{background:#1e293b!important;color:#fff!important;padding:7px 10px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.prof-print td{padding:6px 10px;border-bottom:1px solid #e2e8f0}.prof-print .prow{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px}.prof-print .pf{padding:8px;background:#f8fafc!important;border:1px solid #e2e8f0;border-radius:4px;-webkit-print-color-adjust:exact;print-color-adjust:exact}.prof-print .pfl{font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b}.prof-print .pfv{font-size:13px;font-weight:600;color:#1e293b}.prof-print .rf{margin-top:20px;font-size:10px;color:#94a3b8;text-align:right}}
.prof-print{display:none}
.prof-grid{display:grid;grid-template-columns:1fr 2fr;gap:18px;align-items:start}
@media(max-width:800px){.prof-grid{grid-template-columns:1fr}}
.pf-card{background:var(--bg-card,#1e293b);border:1px solid var(--border-color,#2d3f55);border-radius:12px;padding:20px}
.pf-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:#fff;margin:0 auto 14px}
.pf-name{text-align:center;font-size:17px;font-weight:800;color:var(--text-primary,#e2e8f0);margin-bottom:4px}
.pf-code{text-align:center;margin-bottom:14px}
.pf-field{margin-bottom:10px}
.pf-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);margin-bottom:3px}
.pf-value{font-size:13px;color:var(--text-primary,#e2e8f0)}
.ptbl{width:100%;border-collapse:collapse}
.ptbl th{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);padding:9px 14px;border-bottom:2px solid var(--border-color,#2d3f55);background:rgba(255,255,255,.015);text-align:left}
.ptbl td{padding:9px 14px;font-size:13px;color:var(--text-primary,#e2e8f0);border-bottom:1px solid rgba(255,255,255,.04)}
.ptbl tbody tr:hover{background:rgba(99,102,241,.05)}
</style>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
  <div>
    <div style="font-size:17px;font-weight:800;color:var(--text-primary,#e2e8f0);">Student Profile</div>
    <div style="font-size:12px;color:var(--text-muted,#64748b);">{{ $student->student_code }}</div>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="{{ route('reports.students') }}" class="btn btn-secondary btn-sm">← Back</a>
    <button class="btn btn-secondary btn-sm" onclick="window.print()" style="border-color:rgba(99,102,241,.4);color:#818cf8;">🖨️ Print Profile</button>
    <a href="{{ route('reports.payment.receipt', $student->enrollments->flatMap->payments->last()?->id ?? 0) }}" class="btn btn-primary btn-sm" style="font-size:12px;">🧾 Last Receipt</a>
  </div>
</div>

<div class="prof-grid">
  {{-- Left: Info --}}
  <div>
    <div class="pf-card">
      <div class="pf-avatar">{{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}</div>
      <div class="pf-name">{{ $student->first_name }} {{ $student->last_name }}</div>
      <div class="pf-code"><span class="badge secondary">{{ $student->student_code }}</span></div>
      @if($student->study_status === 'studying')
        <div style="text-align:center;margin-bottom:14px;"><span class="badge active">● Studying</span></div>
      @else
        <div style="text-align:center;margin-bottom:14px;"><span class="badge expired">{{ ucfirst($student->study_status ?? '—') }}</span></div>
      @endif

      @foreach([['Gender', ucfirst($student->gender ?? '—')],['Phone', $student->parent_phone ?? '—'],['Parent', $student->parent_name ?? '—'],['Address', $student->address ?? '—'],['Registered', $student->registration_date?->format('d M Y') ?? '—']] as [$lbl,$val])
      <div class="pf-field"><div class="pf-label">{{ $lbl }}</div><div class="pf-value">{{ $val }}</div></div>
      @endforeach
    </div>
  </div>

  {{-- Right: Enrollments + Payments --}}
  <div>
    @forelse($student->enrollments as $enrollment)
    <div class="pf-card" style="margin-bottom:14px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div>
          <div style="font-size:14px;font-weight:700;color:var(--text-primary,#e2e8f0);">
            {{ $enrollment->classroom?->name ?? '—' }}
            @if($enrollment->is_current)<span class="badge active" style="margin-left:6px;font-size:10px;">Current</span>@endif
          </div>
          <div style="font-size:11px;color:var(--text-muted,#64748b);">
            {{ $enrollment->grade?->name ?? '—' }} · {{ $enrollment->grade?->term?->name ?? '—' }}
            · {{ $enrollment->enrollment_date?->format('d M Y') ?? '' }}
          </div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:18px;font-weight:800;color:#34d399;">${{ number_format($enrollment->payments->sum('amount'),2) }}</div>
          <div style="font-size:10px;color:var(--text-muted,#64748b);">Total paid</div>
        </div>
      </div>
      @if($enrollment->payments->isNotEmpty())
      <table class="ptbl">
        <thead><tr><th>#</th><th>Date</th><th>Plan</th><th>Method</th><th style="text-align:right;">Amount</th><th>Valid Until</th><th>Receipt</th></tr></thead>
        <tbody>
          @foreach($enrollment->payments->sortByDesc('payment_date') as $j => $pay)
          @php $isActive = $pay->end_study_date && now()->lte($pay->end_study_date); @endphp
          <tr>
            <td style="color:var(--text-muted,#64748b);font-size:11px;">{{ $j+1 }}</td>
            <td>{{ $pay->payment_date?->format('d M Y') }}</td>
            <td>{{ $pay->tuitionPlan?->name ?? '—' }}</td>
            <td><span class="badge secondary" style="font-size:10px;">{{ strtoupper($pay->payment_method) }}</span></td>
            <td style="text-align:right;"><strong style="color:#34d399;">${{ number_format($pay->amount,2) }}</strong></td>
            <td>
              @if($pay->end_study_date)
                <span style="color:{{ $isActive ? '#34d399' : '#f87171' }};font-size:12px;">
                  {{ $pay->end_study_date->format('d M Y') }}
                </span>
              @else —
              @endif
            </td>
            <td><a href="{{ route('reports.payment.receipt',$pay->id) }}" style="font-size:11px;color:#818cf8;">🧾 View</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <div style="padding:16px;text-align:center;color:var(--text-muted,#64748b);font-size:13px;">No payments recorded.</div>
      @endif
    </div>
    @empty
      <div class="pf-card"><div style="text-align:center;color:var(--text-muted,#64748b);padding:30px;">No enrollments found.</div></div>
    @endforelse
  </div>
</div>

{{-- Print area --}}
<div class="prof-print" id="profPrint">
  <h2>{{ $student->first_name }} {{ $student->last_name }} — Student Profile</h2>
  <div class="sub">Code: {{ $student->student_code }} | Generated: {{ now()->format('d M Y, H:i') }} | By: {{ auth()->user()->full_name ?? 'Admin' }}</div>
  <div class="prow">
    @foreach([['Code',$student->student_code??'—'],['Full Name',$student->first_name.' '.$student->last_name],['Gender',ucfirst($student->gender??'—')],['Phone',$student->parent_phone??'—'],['Parent',$student->parent_name??'—'],['Study Status',ucfirst($student->study_status??'—')]] as [$l,$v])
    <div class="pf"><div class="pfl">{{ $l }}</div><div class="pfv">{{ $v }}</div></div>
    @endforeach
  </div>
  @foreach($student->enrollments as $enrollment)
  <div style="font-weight:700;margin:10px 0 6px;font-size:13px;">📚 {{ $enrollment->classroom?->name ?? '—' }} · {{ $enrollment->grade?->name ?? '—' }}</div>
  <table>
    <thead><tr><th>#</th><th>Date</th><th>Plan</th><th>Method</th><th>Amount ($)</th><th>Valid Until</th></tr></thead>
    <tbody>
      @foreach($enrollment->payments->sortByDesc('payment_date') as $j => $pay)
      <tr><td>{{ $j+1 }}</td><td>{{ $pay->payment_date?->format('d M Y') }}</td><td>{{ $pay->tuitionPlan?->name??'—' }}</td><td>{{ strtoupper($pay->payment_method) }}</td><td>${{ number_format($pay->amount,2) }}</td><td>{{ $pay->end_study_date?->format('d M Y')??'—' }}</td></tr>
      @endforeach
    </tbody>
  </table>
  @endforeach
  <div class="rf">School Register · Student Profile · {{ now()->format('d M Y') }}</div>
</div>
@endsection
