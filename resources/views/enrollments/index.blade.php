@extends('layouts.app')
@section('title', 'Enrollments')
@section('page-title', 'Enrollments')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>All Enrollments</h2>
        <button class="btn btn-primary" onclick="openModal('enrollModal')">+ Add Enrollment</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Classroom</th><th>Term</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($enrollments as $e)
                <tr>
                    <td><strong>{{ $e->student->first_name ?? '' }} {{ $e->student->last_name ?? '' }}</strong></td>
                    <td>{{ $e->classroom->name ?? '—' }}</td>
                    <td>{{ $e->term->name ?? '—' }}</td>
                    <td>{{ $e->enrollment_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $e->status }}">{{ ucfirst($e->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="empty-icon">📝</div><h3>No enrollments</h3></div></td></tr>
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
                    <div class="form-group"><label class="form-label">Classroom *</label><select name="classroom_id" class="form-control" required><option value="">Select</option>@foreach($classrooms as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label class="form-label">Term *</label><select name="term_id" class="form-control" required><option value="">Select</option>@foreach($terms as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                </div>
                <div class="form-group"><label class="form-label">Enrollment Date *</label><input type="date" name="enrollment_date" class="form-control" required value="{{ date('Y-m-d') }}"></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('enrollModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('enrollForm').submit()">Save</button>
        </div>
    </div>
</div>
@endsection
