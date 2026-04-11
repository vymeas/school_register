@extends('layouts.app')
@section('title','Payment Receipt')
@section('page-title','Payment Receipt')
@section('content')
<style>
.rcpt-wrap{max-width:680px;margin:0 auto}
.rcpt-card{background:var(--bg-card,#1e293b);border:1px solid var(--border-color,#2d3f55);border-radius:16px;overflow:hidden}
.rcpt-hdr{background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:24px 28px;display:flex;align-items:center;justify-content:space-between}
.rcpt-hdr-left .school{font-size:18px;font-weight:900;color:#fff;letter-spacing:.5px}
.rcpt-hdr-left .tag{font-size:11px;color:rgba(255,255,255,.7);margin-top:2px}
.rcpt-hdr-right{text-align:right}
.rcpt-hdr-right .ref{font-size:22px;font-weight:900;color:#fff}
.rcpt-hdr-right .date{font-size:11px;color:rgba(255,255,255,.7);margin-top:2px}
.rcpt-body{padding:24px 28px}
.rcpt-section{margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid rgba(255,255,255,.06)}
.rcpt-section:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
.rcpt-section-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted,#64748b);margin-bottom:12px}
.rcpt-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.rcpt-field .lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted,#64748b);margin-bottom:3px}
.rcpt-field .val{font-size:13px;color:var(--text-primary,#e2e8f0)}
.rcpt-amount-box{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.25);border-radius:10px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between}
.rcpt-amount-box .label{font-size:13px;font-weight:700;color:var(--text-primary,#e2e8f0)}
.rcpt-amount-box .amount{font-size:28px;font-weight:900;color:#34d399}
.sig-row{display:grid;grid-template-columns:1fr 1fr;gap:30px;margin-top:20px}
.sig-box{padding-top:40px;border-top:1px solid rgba(255,255,255,.15);text-align:center;font-size:11px;color:var(--text-muted,#64748b)}

@media print{
  body *{visibility:hidden!important}
  .rcpt-wrap,.rcpt-wrap *{visibility:visible!important}
  .rcpt-wrap{position:fixed;top:0;left:0;width:100%;max-width:none;margin:0;padding:20px;background:#fff;display:block!important}
  .rcpt-card{border:1px solid #e2e8f0;border-radius:8px;max-width:680px;margin:0 auto}
  .rcpt-hdr{background:#4f46e5!important;-webkit-print-color-adjust:exact;print-color-adjust:exact;padding:18px 22px}
  .rcpt-body{padding:18px 22px}
  .rcpt-field .lbl{color:#64748b!important}
  .rcpt-field .val{color:#1e293b!important}
  .rcpt-section{border-bottom-color:#e2e8f0!important}
  .rcpt-amount-box{background:#ecfdf5!important;border-color:#a7f3d0!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
  .rcpt-amount-box .label{color:#1e293b!important}
  .rcpt-amount-box .amount{color:#059669!important}
  .sig-box{color:#64748b!important;border-top-color:#e2e8f0!important}
  .no-print{display:none!important}
}
</style>

<div class="rcpt-wrap">
  {{-- Action bar --}}
  <div class="no-print" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
      <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">Back</a>
    </div>
    <div style="display:flex;gap:8px;">
      <button onclick="window.print()" class="btn btn-secondary btn-sm" style="border-color:rgba(99,102,241,.4);color:#818cf8;">Print Receipt</button>
    </div>
  </div>

  {{-- Receipt Card --}}
  <div class="rcpt-card">
    {{-- Header --}}
    <div class="rcpt-hdr">
      <div class="rcpt-hdr-left">
        <div class="school">School Register</div>
        <div class="tag">Official Payment Receipt</div>
      </div>
      <div class="rcpt-hdr-right">
        <div class="ref">{{ $payment->reference_number ?? 'RCP-'.$payment->id }}</div>
        <div class="date">{{ $payment->payment_date?->format('d M Y') }}</div>
      </div>
    </div>

    <div class="rcpt-body">
      {{-- Amount --}}
      <div class="rcpt-amount-box" style="margin-bottom:20px;">
        <div class="label">Amount Paid</div>
        <div class="amount">${{ number_format($payment->amount, 2) }}</div>
      </div>

      {{-- Student info --}}
      <div class="rcpt-section">
        <div class="rcpt-section-title">Student Information</div>
        <div class="rcpt-grid">
          <div class="rcpt-field"><div class="lbl">Full Name</div><div class="val"><strong>{{ $payment->student?->first_name }} {{ $payment->student?->last_name }}</strong></div></div>
          <div class="rcpt-field"><div class="lbl">Student Code</div><div class="val">{{ $payment->student?->student_code ?? '—' }}</div></div>
          <div class="rcpt-field"><div class="lbl">Classroom</div><div class="val">{{ $payment->enrollment?->classroom?->name ?? '—' }}</div></div>
          <div class="rcpt-field"><div class="lbl">Grade</div><div class="val">{{ $payment->enrollment?->grade?->name ?? '—' }}</div></div>
          <div class="rcpt-field"><div class="lbl">Term</div><div class="val">{{ $payment->enrollment?->grade?->term?->name ?? '—' }}</div></div>
          <div class="rcpt-field"><div class="lbl">Contact</div><div class="val">{{ $payment->student?->parent_phone ?? '—' }}</div></div>
        </div>
      </div>

      {{-- Payment details --}}
      <div class="rcpt-section">
        <div class="rcpt-section-title">Payment Details</div>
        <div class="rcpt-grid">
          <div class="rcpt-field"><div class="lbl">Payment Date</div><div class="val"><strong>{{ $payment->payment_date?->format('d M Y') }}</strong></div></div>
          <div class="rcpt-field"><div class="lbl">Method</div><div class="val"><span class="badge secondary">{{ strtoupper($payment->payment_method ?? '—') }}</span></div></div>
          <div class="rcpt-field"><div class="lbl">Tuition Plan</div><div class="val">{{ $payment->tuitionPlan?->name ?? '—' }}</div></div>
          <div class="rcpt-field"><div class="lbl">Reference #</div><div class="val">{{ $payment->reference_number ?? '—' }}</div></div>
          <div class="rcpt-field"><div class="lbl">Start Date</div><div class="val">{{ $payment->start_study_date?->format('d M Y') ?? '—' }}</div></div>
          <div class="rcpt-field">
            <div class="lbl">Expiry Date</div>
            @php $isActive = $payment->end_study_date && now()->lte($payment->end_study_date); @endphp
            <div class="val">
              @if($payment->end_study_date)
                <strong style="color:{{ $isActive ? '#34d399' : '#f87171' }};">{{ $payment->end_study_date->format('d M Y') }}</strong>
                <span style="font-size:10px;margin-left:4px;">{{ $isActive ? '✓ Active' : '✗ Expired' }}</span>
              @else —
              @endif
            </div>
          </div>
        </div>
        @if($payment->note)
        <div class="rcpt-field" style="margin-top:12px;">
          <div class="lbl">Note</div>
          <div class="val">{{ $payment->note }}</div>
        </div>
        @endif
      </div>

      {{-- Created by --}}
      <div class="rcpt-section">
        <div class="rcpt-grid">
          <div class="rcpt-field"><div class="lbl">Recorded By</div><div class="val">{{ $payment->creator?->full_name ?? $payment->creator?->name ?? '—' }}</div></div>
          <div class="rcpt-field"><div class="lbl">Record Date</div><div class="val">{{ $payment->created_at?->format('d M Y, H:i') }}</div></div>
        </div>
      </div>

      {{-- Signature lines --}}
      <div class="sig-row">
        <div class="sig-box">Cashier Signature</div>
        <div class="sig-box">Student / Parent Signature</div>
      </div>

      {{-- Footer note --}}
      <div style="margin-top:20px;text-align:center;font-size:11px;color:var(--text-muted,#64748b);">
        This is an official receipt. Please keep it for your records.
      </div>
    </div>
  </div>
</div>
@endsection
