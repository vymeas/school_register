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
        </div>
        <button class="btn btn-primary" onclick="openModal('studentModal')">+ Add Student</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Classroom</th>
                    <th>Parent</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($students as $student)
                <tr>
                    <td><strong>{{ $student->student_code }}</strong></td>
                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td>{{ ucfirst($student->gender) }}</td>
                    <td>{{ $student->classroom->name ?? '—' }}</td>
                    <td>{{ $student->parent_name ?? '—' }}</td>
                    <td><span class="badge {{ $student->status }}">{{ ucfirst($student->status) }}</span></td>
                    <td>{{ $student->registration_date ? $student->registration_date->format('d M Y') : '—' }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-secondary">👁</a>
                            <button class="btn btn-sm btn-secondary" onclick="editStudent({{ $student->id }})">✏️</button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/students/{{ $student->id }}', 'student')">🗑</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
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
                        <label class="form-label">Classroom</label>
                        <select name="classroom_id" class="form-control">
                            <option value="">Select Classroom</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Term</label>
                        <select name="term_id" class="form-control">
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Parent Name</label>
                        <input type="text" name="parent_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parent Phone</label>
                        <input type="text" name="parent_phone" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Emergency Contact</label>
                    <input type="text" name="emergency_contact" class="form-control">
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
if (new URLSearchParams(window.location.search).get('action') === 'create') openModal('studentModal');
</script>
@endpush
@endsection
