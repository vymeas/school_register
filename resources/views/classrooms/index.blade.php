@extends('layouts.app')
@section('title', 'Classrooms')
@section('page-title', 'Classrooms')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>All Classrooms</h2>
        <button class="btn btn-primary" onclick="openModal('classroomModal')">+ Add Classroom</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Grade</th><th>Capacity</th><th>Teacher</th><th>Students</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($classrooms as $classroom)
                <tr>
                    <td><strong>{{ $classroom->name }}</strong></td>
                    <td>{{ $classroom->grade->name ?? '—' }}</td>
                    <td>{{ $classroom->capacity }}</td>
                    <td>{{ $classroom->teacher->name ?? '—' }}</td>
                    <td>{{ $classroom->students_count }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/classrooms/{{ $classroom->id }}', 'classroom')">🗑</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><div class="empty-icon">🏫</div><h3>No classrooms</h3><p>Create your first classroom.</p><button class="btn btn-primary" onclick="openModal('classroomModal')">+ Add Classroom</button></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="classroomModal">
    <div class="modal">
        <div class="modal-header"><h3>Add Classroom</h3><button class="modal-close" onclick="closeModal('classroomModal')">✕</button></div>
        <div class="modal-body">
            <form id="classroomForm" method="POST" action="{{ route('classrooms.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Term *</label>
                    <select id="termSelect" class="form-control" required>
                        <option value="">Select Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Grade *</label>
                    <select name="grade_id" id="gradeSelect" class="form-control" required disabled>
                        <option value="">Select Term first</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" data-term-id="{{ $grade->term_id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" placeholder="e.g. Grade 1A" required></div>
                    <div class="form-group"><label class="form-label">Capacity</label><input type="number" name="capacity" class="form-control" value="30" min="1"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('classroomModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('classroomForm').submit()">Save</button>
        </div>
    </div>
</div>

<script>
document.getElementById('termSelect').addEventListener('change', function () {
    const termId = this.value;
    const gradeSelect = document.getElementById('gradeSelect');
    const options = gradeSelect.querySelectorAll('option[data-term-id]');

    // Reset grade select
    gradeSelect.value = '';

    if (!termId) {
        gradeSelect.disabled = true;
        gradeSelect.querySelector('option[value=""]').textContent = 'Select Term first';
        options.forEach(opt => opt.style.display = 'none');
        return;
    }

    gradeSelect.disabled = false;
    gradeSelect.querySelector('option[value=""]').textContent = 'Select Grade';

    let hasVisible = false;
    options.forEach(opt => {
        if (opt.dataset.termId === termId) {
            opt.style.display = '';
            hasVisible = true;
        } else {
            opt.style.display = 'none';
        }
    });

    if (!hasVisible) {
        gradeSelect.querySelector('option[value=""]').textContent = 'No grades for this term';
    }
});
</script>
@endsection
