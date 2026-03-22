@extends('layouts.app')
@section('title', 'Enrollments')
@section('page-title', 'Enrollments')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="toolbar">
            <h2>All Enrollments</h2>
            <select class="form-control" style="width:auto;" id="studentFilter">
                <option value="">All Students</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ (string) request('student_id') === (string) $student->id ? 'selected' : '' }}>
                        {{ $student->student_code }} — {{ $student->first_name }} {{ $student->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary" onclick="openModal('enrollModal')">+ Add Enrollment</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Term</th><th>Grade</th><th>Classroom</th><th>Start</th><th>End (Term)</th><th>Current</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($enrollments as $e)
                <tr>
                    <td><strong>{{ $e->student->first_name ?? '' }} {{ $e->student->last_name ?? '' }}</strong></td>
                    <td>{{ $e->term->name ?? '—' }}</td>
                    <td>{{ $e->grade->name ?? '—' }}</td>
                    <td>{{ $e->classroom->name ?? '—' }}</td>
                    <td>{{ $e->start_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if($e->term?->end_date)
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                {{ $e->term->end_date->format('d M Y') }}
                                <span style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; padding:1px 6px; background:rgba(99,102,241,0.1); color:#818cf8; border-radius:8px;">term</span>
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($e->is_current)
                            <span class="badge active">Yes</span>
                        @else
                            <span class="badge expired">No</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $e->status }}">{{ ucfirst($e->status) }}</span></td>
                    <td>
                        @if($e->is_current)
                            <div class="btn-group">
                                <button class="btn btn-sm btn-secondary" onclick="openUpgradeModal({{ $e->id }}, {{ $e->term_id }}, {{ $e->grade_id }})" data-tip="Upgrade to Next Grade">⬆ Upgrade</button>
                                <button class="btn btn-sm btn-secondary" onclick="openTransferModal({{ $e->id }}, {{ $e->term_id }}, {{ $e->grade_id }}, {{ $e->classroom_id }})" data-tip="Transfer Classroom">⇄ Transfer</button>
                            </div>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9"><div class="empty-state"><div class="empty-icon">📝</div><h3>No enrollments</h3></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($enrollments->hasPages())
    <div class="card-footer">
        <div class="pagination-info">Showing {{ $enrollments->firstItem() }}–{{ $enrollments->lastItem() }} of {{ $enrollments->total() }}</div>
        <div class="pagination">@for($i=1;$i<=$enrollments->lastPage();$i++)<a href="{{ $enrollments->url($i) }}" class="page-btn {{ $enrollments->currentPage()==$i?'active':'' }}">{{ $i }}</a>@endfor</div>
    </div>
    @endif
</div>

<div class="modal-overlay" id="enrollModal">
    <div class="modal">
        <div class="modal-header"><h3>Add Enrollment</h3><button class="modal-close" onclick="closeModal('enrollModal')">✕</button></div>
        <div class="modal-body">
            <form id="enrollForm" method="POST" action="{{ route('enrollments.store') }}">
                @csrf
                <div class="form-group"><label class="form-label">Student *</label><select name="student_id" class="form-control" required><option value="">Select</option>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->student_code }} — {{ $s->first_name }} {{ $s->last_name }}</option>@endforeach</select></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Term *</label><select name="term_id" class="form-control" required id="createTermSelect"><option value="">Select</option>@foreach($terms as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label class="form-label">Grade *</label><select name="grade_id" class="form-control" required id="createGradeSelect"><option value="">Select Grade</option>@foreach($grades as $g)<option value="{{ $g->id }}" data-term-id="{{ $g->term_id }}">{{ $g->name }}</option>@endforeach</select></div>
                </div>
                <div class="form-group"><label class="form-label">Classroom *</label><select name="classroom_id" class="form-control" required id="createClassroomSelect"><option value="">Select Classroom</option>@foreach($classrooms as $c)<option value="{{ $c->id }}" data-term-id="{{ $c->grade->term_id ?? '' }}" data-grade-id="{{ $c->grade_id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}"></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('enrollModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('enrollForm').submit()">Save</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="upgradeModal">
    <div class="modal">
        <div class="modal-header"><h3>Upgrade Enrollment</h3><button class="modal-close" onclick="closeModal('upgradeModal')">✕</button></div>
        <div class="modal-body">
            <form id="upgradeForm" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Term *</label><select name="term_id" class="form-control" required id="upgradeTermSelect"><option value="">Select</option>@foreach($terms as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label class="form-label">Grade *</label><select name="grade_id" class="form-control" required id="upgradeGradeSelect"><option value="">Select Grade</option>@foreach($grades as $g)<option value="{{ $g->id }}" data-term-id="{{ $g->term_id }}">{{ $g->name }}</option>@endforeach</select></div>
                </div>
                <div class="form-group"><label class="form-label">Classroom *</label><select name="classroom_id" class="form-control" required id="upgradeClassroomSelect"><option value="">Select Classroom</option>@foreach($classrooms as $c)<option value="{{ $c->id }}" data-term-id="{{ $c->grade->term_id ?? '' }}" data-grade-id="{{ $c->grade_id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}"></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('upgradeModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('upgradeForm').submit()">Save</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="transferModal">
    <div class="modal">
        <div class="modal-header"><h3>Transfer Enrollment</h3><button class="modal-close" onclick="closeModal('transferModal')">✕</button></div>
        <div class="modal-body">
            <form id="transferForm" method="POST">
                @csrf
                <div class="form-group"><label class="form-label">New Classroom *</label><select name="classroom_id" class="form-control" required id="transferClassroomSelect"><option value="">Select Classroom</option>@foreach($classrooms as $c)<option value="{{ $c->id }}" data-term-id="{{ $c->grade->term_id ?? '' }}" data-grade-id="{{ $c->grade_id }}">{{ $c->name }}</option>@endforeach</select></div>
                <div class="form-group"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}"></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('transferModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('transferForm').submit()">Save</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('studentFilter').addEventListener('change', function() {
    const params = new URLSearchParams(window.location.search);
    if (this.value) params.set('student_id', this.value);
    else params.delete('student_id');
    params.delete('page');
    window.location.search = params.toString();
});

function filterSelectByTermGrade(termSelectId, gradeSelectId, classroomSelectId) {
    const termSelect = document.getElementById(termSelectId);
    const gradeSelect = document.getElementById(gradeSelectId);
    const classroomSelect = document.getElementById(classroomSelectId);
    const termId = termSelect.value;
    const gradeId = gradeSelect.value;

    gradeSelect.querySelectorAll('option[data-term-id]').forEach(option => {
        option.hidden = !!termId && option.dataset.termId !== termId;
    });

    classroomSelect.querySelectorAll('option[data-grade-id]').forEach(option => {
        const sameTerm = !termId || option.dataset.termId === termId;
        const sameGrade = !gradeId || option.dataset.gradeId === gradeId;
        option.hidden = !(sameTerm && sameGrade);
    });
}

document.getElementById('createTermSelect').addEventListener('change', function() {
    document.getElementById('createGradeSelect').value = '';
    document.getElementById('createClassroomSelect').value = '';
    filterSelectByTermGrade('createTermSelect', 'createGradeSelect', 'createClassroomSelect');
});

document.getElementById('createGradeSelect').addEventListener('change', function() {
    document.getElementById('createClassroomSelect').value = '';
    filterSelectByTermGrade('createTermSelect', 'createGradeSelect', 'createClassroomSelect');
});

document.getElementById('upgradeTermSelect').addEventListener('change', function() {
    document.getElementById('upgradeGradeSelect').value = '';
    document.getElementById('upgradeClassroomSelect').value = '';
    filterSelectByTermGrade('upgradeTermSelect', 'upgradeGradeSelect', 'upgradeClassroomSelect');
});

document.getElementById('upgradeGradeSelect').addEventListener('change', function() {
    document.getElementById('upgradeClassroomSelect').value = '';
    filterSelectByTermGrade('upgradeTermSelect', 'upgradeGradeSelect', 'upgradeClassroomSelect');
});

function openUpgradeModal(enrollmentId, termId, gradeId) {
    const form = document.getElementById('upgradeForm');
    form.action = `/enrollments/${enrollmentId}/upgrade`;
    document.getElementById('upgradeTermSelect').value = termId || '';
    document.getElementById('upgradeGradeSelect').value = gradeId || '';
    filterSelectByTermGrade('upgradeTermSelect', 'upgradeGradeSelect', 'upgradeClassroomSelect');
    openModal('upgradeModal');
}

function openTransferModal(enrollmentId, termId, gradeId, currentClassroomId) {
    const form = document.getElementById('transferForm');
    form.action = `/enrollments/${enrollmentId}/transfer`;
    const classroomSelect = document.getElementById('transferClassroomSelect');
    classroomSelect.value = '';
    classroomSelect.querySelectorAll('option[data-grade-id]').forEach(option => {
        const sameTerm = !termId || option.dataset.termId === String(termId);
        const sameGrade = !gradeId || option.dataset.gradeId === String(gradeId);
        const notCurrent = option.value !== String(currentClassroomId);
        option.hidden = !(sameTerm && sameGrade && notCurrent);
    });
    openModal('transferModal');
}

filterSelectByTermGrade('createTermSelect', 'createGradeSelect', 'createClassroomSelect');
</script>
@endpush
@endsection
