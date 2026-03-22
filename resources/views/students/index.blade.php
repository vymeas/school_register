@extends('layouts.app')
@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="toolbar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Search students..." value="{{ request('search') }}">
            </div>
            <select class="form-control" style="width:auto;" id="statusFilter">
                <option value="">All Status</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
            </select>
            <select class="form-control" style="width:auto;" id="termFilter">
                <option value="">All Terms</option>
                @foreach($terms as $term)
                    <option value="{{ $term->id }}" {{ (string) request('term_id') === (string) $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                @endforeach
            </select>
            <select class="form-control" style="width:auto;" id="turnFilter">
                <option value="">All Turns</option>
                @foreach($turns as $turn)
                    <option value="{{ $turn->id }}" {{ (string) request('turn_id') === (string) $turn->id ? 'selected' : '' }}>{{ $turn->name }}</option>
                @endforeach
            </select>
            <select class="form-control" style="width:auto;" id="classroomFilter">
                <option value="">All Classrooms</option>
                @foreach($classrooms as $classroom)
                    <option
                        value="{{ $classroom->id }}"
                        data-term-id="{{ $classroom->grade->term_id ?? '' }}"
                        data-turn-id="{{ $classroom->turn_id ?? '' }}"
                        {{ (string) request('classroom_id') === (string) $classroom->id ? 'selected' : '' }}
                    >
                        {{ $classroom->name }}
                    </option>
                @endforeach
            </select>
            <select class="form-control" style="width:auto;" id="studyStatusFilter">
                <option value="">All Study Status</option>
                <option value="studying" {{ request('study_status')=='studying'?'selected':'' }}>📖 Studying</option>
                <option value="dropped"  {{ request('study_status')=='dropped' ?'selected':'' }}>🚫 Dropped</option>
            </select>
        </div>
        <button class="btn btn-primary" onclick="openModal('studentModal')">+ Add Student</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'student_code', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Code</a></th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'first_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Name</a></th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'date_of_birth', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Date Of Birth</a></th>
                    <th>Teacher</th>
                    <th>Current Enrollment</th>
                    <th>Study Status</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($students as $student)
                @php $enr = $student->enrollments->first(); @endphp
                <tr>
                    <td><strong>{{ $student->student_code }}</strong></td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : '—' }}</td>
                    <td>{{ $enr?->classroom?->teacher?->name ?? '—' }}</td>
                    <td>
                        @if($enr)
                            <div style="line-height:1.5;">
                                <div style="font-size:13px; font-weight:700; color:var(--accent-primary);">
                                    🏫 {{ $enr->classroom->name ?? '—' }}
                                </div>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                    {{ $enr->term->name ?? '—' }}
                                    &rsaquo; {{ $enr->grade->name ?? $enr->classroom->grade->name ?? '—' }}
                                    @if($enr->classroom->turn ?? null)
                                        &rsaquo; {{ $enr->classroom->turn->name }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <span style="font-size:12px; color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    {{-- Study Status --}}
                    <td>
                        @if($student->study_status === 'studying')
                            <span class="badge active" style="background:rgba(16,185,129,0.12); color:#10b981;">📖 Studying</span>
                        @else
                            <span class="badge expired" style="background:rgba(239,68,68,0.1); color:#ef4444;">🚫 Dropped</span>
                        @endif
                    </td>
                    {{-- Payment Status --}}
                    <td>
                        @php
                            $latestPay = $student->latestPayment;
                            $paidUntil = $latestPay?->end_study_date;
                            $payStatus = $paidUntil
                                ? (now()->lte($paidUntil) ? 'active' : 'expired')
                                : 'none';
                        @endphp
                        @if($payStatus === 'active')
                            <span class="badge active">✓ Active</span>
                            <div style="font-size:10px; color:var(--text-muted); margin-top:3px;">
                                Paid until: <strong style="color:var(--text-primary);">{{ $paidUntil->format('d M Y') }}</strong>
                            </div>
                        @elseif($payStatus === 'expired')
                            <span class="badge expired">✕ Expired</span>
                            <div style="font-size:10px; color:var(--text-muted); margin-top:3px;">
                                Was: <strong>{{ $paidUntil->format('d M Y') }}</strong>
                            </div>
                        @else
                            <span class="badge" style="background:rgba(100,116,139,0.15); color:#94a3b8;">— No Payment</span>
                        @endif
                    </td>

                    <td>
                        <div class="btn-group">
                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-secondary" data-tip="View Profile">👁</a>
                            <button class="btn btn-sm btn-secondary" onclick="editStudent({{ $student->id }})" data-tip="Edit Student">✏️</button>
                            <button class="btn btn-sm btn-success" onclick="openPayModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->student_code }}')" data-tip="Record Payment">💳</button>
                            {{-- Quick study status toggle --}}
                            <form method="POST" action="{{ route('students.study-status', $student) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                @if($student->study_status === 'studying')
                                    <input type="hidden" name="study_status" value="dropped">
                                    <button type="submit" class="btn btn-sm btn-warning"
                                        title="Mark as Dropped"
                                        data-tip="Mark as Dropped"
                                        onclick="return confirm('Mark {{ addslashes($student->full_name) }} as Dropped?')">🚫</button>
                                @else
                                    <input type="hidden" name="study_status" value="studying">
                                    <button type="submit" class="btn btn-sm btn-success"
                                        title="Mark as Studying"
                                        data-tip="Mark as Studying"
                                        onclick="return confirm('Mark {{ addslashes($student->full_name) }} as Studying again?')">📖</button>
                                @endif
                            </form>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/students/{{ $student->id }}', 'student')" data-tip="Delete Student">🗑</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">🎓</div>
                            <h3>No students found</h3>
                            <p>Start by adding your first student.</p>
                            <button class="btn btn-primary" onclick="openModal('studentModal')">+ Add Student</button>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
    <div class="card-footer">
        <div class="pagination-info">Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}</div>
        <div class="pagination">
            @for($i = 1; $i <= $students->lastPage(); $i++)
                <a href="{{ $students->url($i) }}" class="page-btn {{ $students->currentPage()==$i?'active':'' }}">{{ $i }}</a>
            @endfor
        </div>
    </div>
    @endif
</div>

{{-- Quick Pay Modal --}}
<div class="modal-overlay" id="quickPayModal">
    <div class="modal">
        <div class="modal-header">
            <div>
                <h3>💳 Record Payment</h3>
                <p id="payModalStudentInfo" style="font-size:12px; color:var(--text-muted); margin-top:2px;"></p>
            </div>
            <button class="modal-close" onclick="closeModal('quickPayModal')">✕</button>
        </div>
        <div class="modal-body">
            <form id="quickPayForm" method="POST" action="{{ route('payments.store') }}">
                @csrf
                <input type="hidden" name="student_id" id="payStudentId">

                <div class="form-group">
                    <label class="form-label">Enrollment *</label>
                    <select name="enrollment_id" id="payEnrollmentSelect" class="form-control" required>
                        <option value="">Select Enrollment</option>
                        @foreach($enrollments as $enrollment)
                            <option
                                value="{{ $enrollment->id }}"
                                data-student-id="{{ $enrollment->student_id }}"
                            >
                                {{ $enrollment->classroom->name ?? '—' }} · {{ $enrollment->term->name ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tuition Plan *</label>
                    <select name="tuition_plan_id" id="payPlanSelect" class="form-control" required>
                        <option value="">Select Plan</option>
                        @foreach($tuitionPlans as $plan)
                            <option value="{{ $plan->id }}" data-price="{{ $plan->price }}">
                                {{ $plan->name }} — ${{ number_format($plan->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Amount ($) *</label>
                        <input type="number" name="amount" id="payAmount" class="form-control" step="0.01" required readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Payment Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="aba">ABA</option>
                            <option value="acleda">ACLEDA</option>
                            <option value="wing">Wing</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reference #</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Optional">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Note</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="Optional note"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('quickPayModal')">Cancel</button>
            <button class="btn btn-success" onclick="document.getElementById('quickPayForm').submit()">💳 Save Payment</button>
        </div>
    </div>
</div>

{{-- Create/Edit Modal --}}
<div class="modal-overlay" id="studentModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="studentModalTitle">Add New Student</h3>
            <button class="modal-close" onclick="closeModal('studentModal')">✕</button>
        </div>
        <div class="modal-body">
            <form id="studentForm" method="POST" action="{{ route('students.store') }}">
                @csrf
                <input type="hidden" name="_method" id="studentMethod" value="POST">
                <div class="form-group">
                    <label class="form-label">Student ID (Leave empty to auto-generate)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="student_code" id="studentCodeInput" class="form-control" placeholder="e.g. STU-0001">
                        <button type="button" class="btn btn-secondary" onclick="generateStudentCode()">Generate</button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-control" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Place of Birth</label>
                        <input type="text" name="place_of_birth" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Father Name</label>
                        <input type="text" name="father_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Father Contact</label>
                        <input type="text" name="father_contact" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mother Name</label>
                        <input type="text" name="mother_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mother Contact</label>
                        <input type="text" name="mother_contact" class="form-control">
                    </div>
                </div>
                <hr>
                
                <hr>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Health</label>
                        <textarea name="health" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('studentModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('studentForm').submit()">Save Student</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const params = new URLSearchParams(window.location.search);
        params.set('search', this.value);
        window.location.search = params.toString();
    }
});
document.getElementById('statusFilter').addEventListener('change', function() {
    const params = new URLSearchParams(window.location.search);
    if (this.value) params.set('status', this.value);
    else params.delete('status');
    window.location.search = params.toString();
});

document.getElementById('studyStatusFilter').addEventListener('change', function() {
    const params = new URLSearchParams(window.location.search);
    if (this.value) params.set('study_status', this.value);
    else params.delete('study_status');
    params.delete('page');
    window.location.search = params.toString();
});

const termFilter = document.getElementById('termFilter');
const turnFilter = document.getElementById('turnFilter');
const classroomFilter = document.getElementById('classroomFilter');

function applyFilterParam(params, key, value) {
    if (value) {
        params.set(key, value);
        return;
    }
    params.delete(key);
}

function updateClassroomFilterOptions() {
    const selectedTerm = termFilter.value;
    const selectedTurn = turnFilter.value;
    const options = classroomFilter.querySelectorAll('option');

    options.forEach(option => {
        if (!option.value) {
            option.hidden = false;
            return;
        }

        const optionTerm = option.dataset.termId;
        const optionTurn = option.dataset.turnId;
        const matchTerm = !selectedTerm || optionTerm === selectedTerm;
        const matchTurn = !selectedTurn || optionTurn === selectedTurn;
        option.hidden = !(matchTerm && matchTurn);
    });

    const selectedOption = classroomFilter.options[classroomFilter.selectedIndex];
    if (selectedOption && selectedOption.hidden) {
        classroomFilter.value = '';
    }
}

function applyToolbarFilters() {
    const params = new URLSearchParams(window.location.search);
    applyFilterParam(params, 'status', document.getElementById('statusFilter').value);
    applyFilterParam(params, 'term_id', termFilter.value);
    applyFilterParam(params, 'turn_id', turnFilter.value);
    applyFilterParam(params, 'classroom_id', classroomFilter.value);
    params.delete('page');
    window.location.search = params.toString();
}

termFilter.addEventListener('change', function() {
    updateClassroomFilterOptions();
    applyToolbarFilters();
});

turnFilter.addEventListener('change', function() {
    updateClassroomFilterOptions();
    applyToolbarFilters();
});

classroomFilter.addEventListener('change', applyToolbarFilters);

updateClassroomFilterOptions();

// Reset form for "Add"
const originalOpenModal = openModal;
window.openModal = function(id) {
    if (id === 'studentModal') {
        document.getElementById('studentModalTitle').innerText = 'Add New Student';
        document.getElementById('studentForm').action = "{{ route('students.store') }}";
        document.getElementById('studentMethod').value = "POST";
        document.getElementById('studentForm').reset();
    }
    originalOpenModal(id);
};

async function editStudent(id) {
    try {
        const response = await fetch(`/api/students/${id}`);
        const data = await response.json();
        const student = data.student;

        if (student) {
            document.getElementById('studentModalTitle').innerText = 'Edit Student';
            const form = document.getElementById('studentForm');
            form.action = `/students/${id}`;
            document.getElementById('studentMethod').value = "PUT";

            // Populate form fields
            form.querySelector('[name="student_code"]').value = student.student_code || '';
            form.querySelector('[name="first_name"]').value = student.first_name || '';
            form.querySelector('[name="last_name"]').value = student.last_name || '';
            form.querySelector('[name="gender"]').value = student.gender || 'male';
            form.querySelector('[name="date_of_birth"]').value = student.date_of_birth ? student.date_of_birth.split('T')[0] : '';
            form.querySelector('[name="place_of_birth"]').value = student.place_of_birth || '';
            form.querySelector('[name="address"]').value = student.address || '';
            form.querySelector('[name="father_name"]').value = student.father_name || '';
            form.querySelector('[name="father_contact"]').value = student.father_contact || '';
            form.querySelector('[name="mother_name"]').value = student.mother_name || '';
            form.querySelector('[name="mother_contact"]').value = student.mother_contact || '';
            form.querySelector('[name="health"]').value = student.health || '';

            originalOpenModal('studentModal');
        }
    } catch (error) {
        console.error('Error fetching student details:', error);
        alert('Failed to load student details.');
    }
}

async function generateStudentCode() {
    try {
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = '...';
        btn.disabled = true;

        const response = await fetch('/api/students/generate-code');
        const data = await response.json();
        
        if (data.code) {
            document.getElementById('studentCodeInput').value = data.code;
        }
        
        btn.innerText = originalText;
        btn.disabled = false;
    } catch (error) {
        console.error('Error generating student code:', error);
        alert('Failed to generate code. Please try again.');
        event.target.innerText = 'Generate';
        event.target.disabled = false;
    }
}

if (new URLSearchParams(window.location.search).get('action') === 'create') openModal('studentModal');

// ---- Quick Pay ----
document.getElementById('payPlanSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    document.getElementById('payAmount').value = opt.dataset.price || '';
});

function openPayModal(studentId, studentName, studentCode) {
    // Set student info
    document.getElementById('payStudentId').value = studentId;
    document.getElementById('payModalStudentInfo').innerText = `${studentCode} · ${studentName}`;

    // Filter enrollment dropdown to this student only
    const enrollmentSelect = document.getElementById('payEnrollmentSelect');
    let firstMatch = null;
    Array.from(enrollmentSelect.options).forEach(opt => {
        if (!opt.value) return;
        const match = opt.dataset.studentId === String(studentId);
        opt.hidden = !match;
        if (match && !firstMatch) firstMatch = opt.value;
    });

    // Auto-select if only one enrollment
    enrollmentSelect.value = firstMatch || '';

    // Reset plan & amount
    document.getElementById('payPlanSelect').value = '';
    document.getElementById('payAmount').value = '';

    openModal('quickPayModal');
}
</script>
@endpush
@endsection
