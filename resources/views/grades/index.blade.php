@extends('layouts.app')
@section('title', 'Grades')
@section('page-title', 'Grades')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>All Grades</h2>
        <button class="btn btn-primary" onclick="openModal('gradeModal')">+ Add Grade</button>
    </div>
    <div style="padding: 12px 20px; border-bottom: 1px solid var(--border-color, #e2e8f0);">
        <label class="form-label" style="margin-right: 8px; font-weight: 500;">Filter by Term:</label>
        <select id="termFilter" class="form-control" style="display: inline-block; width: auto; min-width: 200px;">
            <option value="">All Terms</option>
            @foreach($terms as $term)
                <option value="{{ $term->id }}">{{ $term->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Term</th><th>Description</th><th>Classrooms</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($grades as $grade)
                <tr data-term-id="{{ $grade->term_id }}">
                    <td><strong>{{ $grade->name }}</strong></td>
                    <td>{{ $grade->term->name ?? '—' }}</td>
                    <td>{{ $grade->description ?? '—' }}</td>
                    <td>{{ $grade->classrooms_count }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-secondary" onclick="editGrade({{ json_encode($grade) }})" data-tip="Edit Grade">✎</button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/grades/{{ $grade->id }}', 'grade')" data-tip="Delete Grade">🗑</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="empty-icon">📚</div><h3>No grades</h3><p>Add the first grade.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal-overlay" id="gradeModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Add Grade</h3>
            <button class="modal-close" onclick="closeModal('gradeModal')">✕</button>
        </div>
        <div class="modal-body">
            <form id="gradeForm" method="POST" action="{{ route('grades.store') }}">
                @csrf
                <div id="methodField"></div>
                <div class="form-group">
                    <label class="form-label">Term *</label>
                    <select name="term_id" id="grade_term_id" class="form-control" required>
                        <option value="">Select Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" id="grade_name" class="form-control" placeholder="e.g. Grade 1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" id="grade_description" class="form-control">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('gradeModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('gradeForm').submit()">Save</button>
        </div>
    </div>
</div>

<script>
// Term filter
document.getElementById('termFilter').addEventListener('change', function () {
    const termId = this.value;
    const rows = document.querySelectorAll('tbody tr[data-term-id]');
    rows.forEach(row => {
        if (!termId || row.dataset.termId === termId) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

function editGrade(grade) {
    document.getElementById('modalTitle').innerText = 'Edit Grade';
    document.getElementById('gradeForm').action = '/grades/' + grade.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    document.getElementById('grade_term_id').value = grade.term_id;
    document.getElementById('grade_name').value = grade.name;
    document.getElementById('grade_description').value = grade.description || '';
    
    openModal('gradeModal');
}

// Reset form when opening for "Add"
const originalOpenModal = window.openModal;
window.openModal = function(id) {
    if (id === 'gradeModal' && event && event.target.innerText.includes('+ Add')) {
        document.getElementById('modalTitle').innerText = 'Add Grade';
        document.getElementById('gradeForm').action = '{{ route("grades.store") }}';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('gradeForm').reset();
    }
    originalOpenModal(id);
};
</script>
@endsection
