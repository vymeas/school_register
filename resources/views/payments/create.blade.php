@extends('layouts.app')
@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<style>
/* ════════════════════════════════════════════════════════════
   PAYMENT FORM — PREMIUM UI
════════════════════════════════════════════════════════════ */

/* Two-column grid — enrollment narrower, payment details wider */
.pf-grid {
    display: grid;
    grid-template-columns: 2fr 3fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 960px) { .pf-grid { grid-template-columns: 1fr; } }

/* Reference # input+button group */
.ref-group {
    display: flex;
    gap: 0;
    border: 1px solid var(--border-color, #2d3f55);
    border-radius: 8px;
    overflow: hidden;
}
.ref-group .form-control {
    border: none !important;
    border-radius: 0 !important;
    flex: 1;
    box-shadow: none !important;
}
.ref-group .form-control:focus { outline: none; }
.ref-gen-btn {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 14px;
    background: rgba(99,102,241,.12);
    border: none;
    border-left: 1px solid var(--border-color, #2d3f55);
    color: #818cf8;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: background .14s, color .14s;
    letter-spacing: .2px;
}
.ref-gen-btn:hover { background: #6366f1; color: #fff; }
.ref-gen-btn svg { width: 13px; height: 13px; }

/* Section panels */
.pf-panel {
    background: var(--bg-card, #1e293b);
    border: 1px solid var(--border-color, #2d3f55);
    border-radius: 14px;
    overflow: hidden;
}
.pf-panel-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border-color, #2d3f55);
    background: rgba(255,255,255,.02);
}
.pf-panel-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.pf-panel-icon.indigo { background: rgba(99,102,241,.18); }
.pf-panel-icon.emerald { background: rgba(16,185,129,.18); }
.pf-panel-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary, #e2e8f0);
    letter-spacing: .3px;
}
.pf-panel-body { padding: 18px; }

/* Field group */
.pf-field { margin-bottom: 16px; }
.pf-field:last-child { margin-bottom: 0; }
.pf-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .6px;
    text-transform: uppercase;
    color: var(--text-muted, #64748b);
    margin-bottom: 7px;
}
.pf-label .req { color: #f87171; margin-left: 2px; }

/* Row of two fields */
.pf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 600px) { .pf-row { grid-template-columns: 1fr; } }

/* ─── ENROLLMENT SEARCH ─── */
.ep-box {
    border: 1.5px solid var(--border-color, #2d3f55);
    border-radius: 10px;
    overflow: hidden;
    transition: border-color .2s;
}
.ep-box.error { border-color: #ef4444; }

/* Search bar */
.ep-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: var(--bg-secondary, #0f172a);
    border-bottom: 1px solid var(--border-color, #2d3f55);
}
.ep-bar-icon { font-size: 14px; color: var(--text-muted, #64748b); flex-shrink: 0; }
.ep-bar-input {
    flex: 1;
    border: none;
    background: transparent;
    color: var(--text-primary, #e2e8f0);
    font-size: 13.5px;
    outline: none;
    min-width: 0;
}
.ep-bar-input::placeholder { color: var(--text-muted, #475569); }
.ep-bar-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
    background: rgba(99,102,241,.15);
    color: #818cf8;
    font-weight: 600;
    flex-shrink: 0;
    display: none;
}
.ep-bar-clear {
    background: none; border: none;
    color: var(--text-muted, #64748b);
    cursor: pointer; font-size: 13px;
    padding: 2px 5px; border-radius: 4px;
    flex-shrink: 0; display: none; line-height: 1;
    transition: color .15s;
}
.ep-bar-clear:hover { color: #f87171; }

/* Results pane */
.ep-list {
    max-height: 260px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #2d3f55 transparent;
    background: var(--bg-secondary, #0f172a);
}
.ep-list::-webkit-scrollbar { width: 4px; }
.ep-list::-webkit-scrollbar-thumb { background: #2d3f55; border-radius: 2px; }

/* Empty / hint state */
.ep-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 8px;
    padding: 30px 16px;
    color: var(--text-muted, #475569);
    font-size: 12.5px;
    text-align: center;
}
.ep-empty-icon { font-size: 28px; opacity: .45; }
.ep-empty strong { color: var(--text-primary, #94a3b8); }

/* Student card */
.ep-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid rgba(255,255,255,.04);
    transition: background .12s;
    position: relative;
}
.ep-card:last-child { border-bottom: none; }
.ep-card:hover { background: rgba(99,102,241,.08); }
.ep-card.chosen  { background: rgba(99,102,241,.14); border-left: 3px solid #6366f1; }

.ep-av {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, #4f46e5, #818cf8);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 12px; color: #fff; letter-spacing: .5px;
}
.ep-info { flex: 1; min-width: 0; }
.ep-info-code {
    font-size: 10px; font-weight: 800;
    color: #818cf8; letter-spacing: .5px; text-transform: uppercase;
    margin-bottom: 1px;
}
.ep-info-name {
    font-size: 13px; font-weight: 600;
    color: var(--text-primary, #e2e8f0);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ep-info-sub {
    font-size: 11px; color: var(--text-muted, #64748b);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-top: 2px;
}
.ep-pick-btn {
    flex-shrink: 0;
    border: 1.5px solid rgba(99,102,241,.35);
    background: rgba(99,102,241,.1);
    color: #818cf8;
    font-size: 11px; font-weight: 700;
    padding: 4px 11px; border-radius: 6px;
    cursor: pointer;
    transition: all .14s;
    white-space: nowrap;
}
.ep-card:hover .ep-pick-btn { background: #6366f1; border-color: #6366f1; color: #fff; }
.ep-card.chosen .ep-pick-btn { background: #10b981; border-color: #10b981; color: #fff; }

.ep-hl { background: rgba(250,204,21,.22); color: #fbbf24; border-radius: 2px; padding: 0 1px; }

/* Selected banner */
.ep-selected-bar {
    display: none;
    align-items: center;
    gap: 12px;
    padding: 11px 14px;
    background: rgba(16,185,129,.08);
    border-top: 1px solid rgba(16,185,129,.2);
}
.ep-selected-bar.show { display: flex; }
.ep-sel-av {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, #059669, #34d399);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; color: #fff;
}
.ep-sel-info { flex: 1; min-width: 0; }
.ep-sel-name {
    font-size: 12.5px; font-weight: 700;
    color: var(--text-primary, #e2e8f0);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ep-sel-sub { font-size: 11px; color: #34d399; }
.ep-sel-change {
    flex-shrink: 0;
    font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 6px;
    border: 1px solid rgba(148,163,184,.25);
    background: transparent; color: var(--text-muted, #94a3b8);
    cursor: pointer; transition: all .14s;
}
.ep-sel-change:hover { background: rgba(255,255,255,.06); color: var(--text-primary, #e2e8f0); }

/* Action bar */
.pf-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 20px;
    border-top: 1px solid var(--border-color, #2d3f55);
    background: rgba(255,255,255,.01);
    margin-top: 20px;
    border-radius: 14px;
    border: 1px solid var(--border-color, #2d3f55);
}
.btn-save {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 24px;
    background: linear-gradient(135deg, #6366f1, #818cf8);
    color: #fff; border: none; border-radius: 8px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    box-shadow: 0 4px 15px rgba(99,102,241,.35);
    transition: all .2s;
    letter-spacing: .2px;
}
.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99,102,241,.45);
}
.btn-cancel {
    padding: 10px 20px;
    background: transparent;
    color: var(--text-muted, #64748b);
    border: 1px solid var(--border-color, #2d3f55);
    border-radius: 8px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    text-decoration: none;
    transition: all .14s;
}
.btn-cancel:hover {
    background: rgba(255,255,255,.04);
    color: var(--text-primary, #e2e8f0);
}

/* method pill buttons */
.method-pills { display: flex; gap: 8px; flex-wrap: wrap; }
.method-pill {
    flex: 1; min-width: 70px;
    padding: 9px 6px;
    border: 1.5px solid var(--border-color, #2d3f55);
    border-radius: 8px;
    background: transparent;
    color: var(--text-muted, #64748b);
    font-size: 12px; font-weight: 700;
    cursor: pointer; text-align: center;
    transition: all .14s;
    display: inline-flex; align-items: center; justify-content: center; gap: 4px;
}
.method-pill:hover { border-color: #6366f1; color: #818cf8; background: rgba(99,102,241,.07); }
.method-pill.active { border-color: #6366f1; color: #fff; background: #6366f1; }
</style>

<form id="paymentForm" method="POST" action="{{ route('payments.store') }}">
@csrf
<input type="hidden" name="student_id"    id="studentIdInput"    value="{{ $selectedStudentId }}">
<input type="hidden" name="enrollment_id" id="enrollmentIdInput" value="">
<input type="hidden" name="payment_method" id="methodInput" value="cash">

{{-- ═══════════════════════════════════════════
      TOP BAR
══════════════════════════════════════════ --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
    <div>
        <div style="font-size:18px; font-weight:800; color:var(--text-primary,#e2e8f0);">Record New Payment</div>
        <div style="font-size:12px; color:var(--text-muted,#64748b); margin-top:2px;">Fill in all required fields and save.</div>
    </div>
    <a href="{{ route('payments.index') }}" class="btn-cancel">Back to Payments</a>
</div>

{{-- ═══════════════════════════════════════════
      TWO-COL GRID
══════════════════════════════════════════ --}}
<div class="pf-grid">

    {{-- ─── LEFT: Enrollment Search ─── --}}
    <div class="pf-panel">
        <div class="pf-panel-header">
            <div class="pf-panel-icon indigo"><i data-lucide="graduation-cap"></i></div>
            <div>
                <div class="pf-panel-title">Student Enrollment</div>
                <div style="font-size:11px;color:var(--text-muted,#64748b);margin-top:1px;">Search and select the enrollment to pay for</div>
            </div>
        </div>
        <div class="pf-panel-body" style="padding:0;">

            {{-- Search box --}}
            <div id="epBox" class="ep-box" style="border:none; border-radius:0;">
                <div class="ep-bar">
                    <span class="ep-bar-icon"><i data-lucide="search" style="width:14px;height:14px;"></i></span>
                    <input
                        type="text"
                        id="epInput"
                        class="ep-bar-input"
                        placeholder="Type student name or code…"
                        autocomplete="off"
                    >
                    <span class="ep-bar-badge" id="epBadge"></span>
                    <button type="button" class="ep-bar-clear" id="epClear"><i data-lucide="x" style="width:18px;height:18px;"></i></button>
                </div>

                {{-- List --}}
                <div class="ep-list" id="epList">
                    <div class="ep-empty" id="epHint">
                        <span class="ep-empty-icon"><i data-lucide="search" style="width:14px;height:14px;"></i></span>
                        <span>Type a student name or ID to search</span>
                    </div>
                </div>

                {{-- Selected banner --}}
                <div class="ep-selected-bar" id="epSelBar">
                    <div class="ep-sel-av">✓</div>
                    <div class="ep-sel-info">
                        <div class="ep-sel-name" id="epSelName"></div>
                        <div class="ep-sel-sub" id="epSelSub"></div>
                    </div>
                    <button type="button" class="ep-sel-change" onclick="epReset()">Change</button>
                </div>
            </div>

            @error('enrollment_id')
                <div style="padding:8px 14px; font-size:12px; color:#f87171;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- ─── RIGHT: Payment Details ─── --}}
    <div class="pf-panel">
        <div class="pf-panel-header">
            <div class="pf-panel-icon emerald"><i data-lucide="banknote" style="width:16px;height:16px;"></i></div>
            <div>
                <div class="pf-panel-title">Payment Details</div>
                <div style="font-size:11px;color:var(--text-muted,#64748b);margin-top:1px;">Plan, amount, method and date</div>
            </div>
        </div>
        <div class="pf-panel-body">

            {{-- Tuition Plan --}}
            <div class="pf-field">
                <label class="pf-label">Tuition Plan <span class="req">*</span></label>
                <select name="tuition_plan_id" class="form-control" required id="planSelect">
                    <option value="">Select Plan</option>
                    @foreach($tuitionPlans as $p)
                        <option
                            value="{{ $p->id }}"
                            data-price="{{ $p->price }}"
                            {{ (string) $selectedPlanId === (string) $p->id ? 'selected' : '' }}
                        >{{ $p->name }} — ${{ number_format($p->price, 2) }}</option>
                    @endforeach
                </select>
                @error('tuition_plan_id') <span style="font-size:11px;color:#f87171;">{{ $message }}</span> @enderror
            </div>

            {{-- Amount & Date --}}
            <div class="pf-row pf-field">
                <div>
                    <label class="pf-label">Amount ($) <span class="req">*</span></label>
                    <input type="number" name="amount" id="amountInput" class="form-control" step="0.01" required readonly placeholder="Auto from plan">
                    @error('amount') <span style="font-size:11px;color:#f87171;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="pf-label">Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                    @error('payment_date') <span style="font-size:11px;color:#f87171;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Payment Method Pills --}}
            <div class="pf-field">
                <label class="pf-label">Payment Method <span class="req">*</span></label>
                <div class="method-pills">
                    <button type="button" class="method-pill active" data-method="cash"   onclick="setMethod('cash',   this)"><i data-lucide="banknote" style="width:14px;height:14px;"></i> Cash</button>
                    <button type="button" class="method-pill"        data-method="aba"    onclick="setMethod('aba',    this)"><i data-lucide="landmark" style="width:14px;height:14px;"></i> ABA</button>
                    <button type="button" class="method-pill"        data-method="acleda" onclick="setMethod('acleda', this)"><i data-lucide="building-2" style="width:14px;height:14px;"></i> ACLEDA</button>
                    <button type="button" class="method-pill"        data-method="wing"   onclick="setMethod('wing',   this)"><i data-lucide="send" style="width:14px;height:14px;"></i> Wing</button>
                </div>
                @error('payment_method') <span style="font-size:11px;color:#f87171;">{{ $message }}</span> @enderror
            </div>

            {{-- Reference --}}
            <div class="pf-field">
                <label class="pf-label" style="display:flex; align-items:center; justify-content:space-between;">
                    <span>Reference #</span>
                    <span style="font-size:10px; color:var(--text-muted,#64748b); font-weight:400; text-transform:none; letter-spacing:0;">Auto-generate or type manually</span>
                </label>
                <div class="ref-group">
                    <input type="text" name="reference_number" id="refInput" class="form-control" placeholder="e.g. REF-20260411-001 or leave blank">
                    <button type="button" class="ref-gen-btn" onclick="genRef()" title="Auto-generate reference number">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6 2.1 2.1M5.6 18.4l2.1-2.1m8.6-8.6 2.1-2.1"/>
                        </svg>
                        Generate
                    </button>
                </div>
                @error('reference_number') <span style="font-size:11px;color:#f87171;">{{ $message }}</span> @enderror
            </div>

            {{-- Note --}}
            <div class="pf-field">
                <label class="pf-label">Note</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Optional notes…"></textarea>
                @error('note') <span style="font-size:11px;color:#f87171;">{{ $message }}</span> @enderror
            </div>

        </div>
    </div>

</div>{{-- end pf-grid --}}

{{-- ═══ ACTION BAR ═══ --}}
<div class="pf-actions">
    <span id="epRequired" style="font-size:12px;color:#f87171;display:none;">Please select a student enrollment first.</span>
    <a href="{{ route('payments.index') }}" class="btn-cancel">Cancel</a>
    <button type="submit" class="btn-save">Save Payment</button>
</div>

</form>

<script>
// ── Data ──
const enrollments          = @json($enrollmentsJson);
const preselectedStudentId = "{{ $selectedStudentId }}";

// ── DOM ──
const epInput   = document.getElementById('epInput');
const epList    = document.getElementById('epList');
const epHint    = document.getElementById('epHint');
const epClear   = document.getElementById('epClear');
const epBadge   = document.getElementById('epBadge');
const epSelBar  = document.getElementById('epSelBar');
const epSelName = document.getElementById('epSelName');
const epSelSub  = document.getElementById('epSelSub');
const epBox     = document.getElementById('epBox');
const enrollIn  = document.getElementById('enrollmentIdInput');
const studentIn = document.getElementById('studentIdInput');
const methodIn  = document.getElementById('methodInput');

let chosenId = null;

// ── Helpers ──
function av(first, last) {
    return ((first[0]||'') + (last[0]||'')).toUpperCase() || '?';
}
function hl(text, q) {
    if (!q) return text;
    const i = text.toLowerCase().indexOf(q.toLowerCase());
    if (i === -1) return text;
    return text.slice(0,i) + `<span class="ep-hl">${text.slice(i,i+q.length)}</span>` + text.slice(i+q.length);
}

// ── Render ──
function renderList(q) {
    const raw = q.trim();
    const ql  = raw.toLowerCase();

    if (!raw) {
        epList.innerHTML = '';
        epList.appendChild(epHint);
        epHint.style.display = '';
        epBadge.style.display = 'none';
        epBadge.textContent = '';
        return;
    }

    const hits = enrollments.filter(e =>
        e.student_code.toLowerCase().includes(ql) ||
        e.first_name.toLowerCase().includes(ql)   ||
        e.last_name.toLowerCase().includes(ql)    ||
        (e.first_name + ' ' + e.last_name).toLowerCase().includes(ql)
    );

    epBadge.textContent   = hits.length ? `${hits.length} found` : '0';
    epBadge.style.display = 'inline-block';

    if (!hits.length) {
        epList.innerHTML = `<div class="ep-empty">
            <span class="ep-empty-icon"><i data-lucide="search" style="width:16px;height:16px;"></i></span>
            <span>No enrollment found for "<strong>${raw}</strong>"</span>
        </div>`;
        return;
    }

    epList.innerHTML = hits.map(e => {
        const full   = `${e.first_name} ${e.last_name}`.trim();
        const chosen = String(e.id) === String(chosenId);
        return `<div class="ep-card ${chosen ? 'chosen' : ''}"
                     onclick="pick('${e.id}','${e.student_id}','${e.student_code}','${full.replace(/'/g,"\\'")}','${e.classroom.replace(/'/g,"\\'")}','${e.term.replace(/'/g,"\\'")}',this)">
            <div class="ep-av">${av(e.first_name, e.last_name)}</div>
            <div class="ep-info">
                <div class="ep-info-code">${hl(e.student_code, raw)}</div>
                <div class="ep-info-name">${hl(full, raw)}</div>
                <div class="ep-info-sub">${e.classroom} · ${e.term}</div>
            </div>
            <div class="ep-pick-btn">${chosen ? '✓ Chosen' : 'Select'}</div>
        </div>`;
    }).join('');
}

// ── Pick ──
function pick(id, sid, code, name, classroom, term, el) {
    chosenId      = id;
    enrollIn.value  = id;
    studentIn.value = sid;

    // update cards
    document.querySelectorAll('.ep-card').forEach(c => {
        c.classList.remove('chosen');
        const b = c.querySelector('.ep-pick-btn');
        if (b) b.textContent = 'Select';
    });
    el.classList.add('chosen');
    const pb = el.querySelector('.ep-pick-btn');
    if (pb) pb.textContent = '✓ Chosen';

    // show banner
    epSelName.textContent = `${code} · ${name}`;
    epSelSub.textContent  = `${classroom}  ·  ${term}`;
    epSelBar.classList.add('show');

    // clear error state
    document.getElementById('epRequired').style.display = 'none';
    epBox.classList.remove('error');
}

// ── Reset ──
function epReset() {
    chosenId = null;
    enrollIn.value  = '';
    studentIn.value = '';
    epSelBar.classList.remove('show');
    epInput.value = '';
    epClear.style.display = 'none';
    epBadge.style.display = 'none';
    epBadge.textContent = '';
    epList.innerHTML = '';
    epList.appendChild(epHint);
    epHint.style.display = '';
    epInput.focus();
}

// ── Search events ──
epInput.addEventListener('input', function () {
    epClear.style.display = this.value ? 'block' : 'none';
    renderList(this.value);
});
epClear.addEventListener('click', function () {
    epInput.value = '';
    this.style.display = 'none';
    renderList('');
    epInput.focus();
});

// ── Payment method pills ──
function setMethod(val, btn) {
    methodIn.value = val;
    document.querySelectorAll('.method-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
}

// ── Tuition plan → amount ──
document.getElementById('planSelect').addEventListener('change', function () {
    document.getElementById('amountInput').value =
        this.options[this.selectedIndex].dataset.price || '';
});

// ── Preselect on load ──
if (preselectedStudentId) {
    const m = enrollments.find(e => String(e.student_id) === String(preselectedStudentId));
    if (m) {
        const full = `${m.first_name} ${m.last_name}`.trim();
        chosenId        = m.id;
        enrollIn.value  = m.id;
        studentIn.value = m.student_id;
        epSelName.textContent = `${m.student_code} · ${full}`;
        epSelSub.textContent  = `${m.classroom}  ·  ${m.term}`;
        epSelBar.classList.add('show');
    }
}
if (document.getElementById('planSelect').value) {
    document.getElementById('planSelect').dispatchEvent(new Event('change'));
}

// ── Auto-generate Reference # ──
function genRef() {
    const now   = new Date();
    const yy    = now.getFullYear();
    const mm    = String(now.getMonth() + 1).padStart(2, '0');
    const dd    = String(now.getDate()).padStart(2, '0');
    const hh    = String(now.getHours()).padStart(2, '0');
    const mi    = String(now.getMinutes()).padStart(2, '0');
    const rand  = String(Math.floor(Math.random() * 900) + 100); // 3-digit random
    const ref   = `REF-${yy}${mm}${dd}-${hh}${mi}-${rand}`;
    const input = document.getElementById('refInput');
    input.value = ref;
    // flash highlight
    input.style.transition = 'background .15s';
    input.style.background = 'rgba(99,102,241,.18)';
    setTimeout(() => { input.style.background = ''; }, 600);
}

// ── Submit guard ──
document.getElementById('paymentForm').addEventListener('submit', function (e) {
    if (!enrollIn.value) {
        e.preventDefault();
        const hint = document.getElementById('epRequired');
        hint.style.display = 'inline';
        epBox.classList.add('error');
        epInput.focus();
        setTimeout(() => {
            hint.style.display = 'none';
            epBox.classList.remove('error');
        }, 4500);
    }
});
</script>
@endsection
