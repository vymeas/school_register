@extends('layouts.app')
@section('title', 'Teachers')
@section('page-title', 'Teachers')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="toolbar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Search by name, email, or ID..." value="{{ request('search') }}">
            </div>
        </div>
        <button class="btn btn-primary" onclick="openModal('teacherModal')">+ Add Teacher</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Teacher ID</th><th>Name</th><th>Gender</th><th>Date of Birth</th><th>Phone</th><th>Email</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse ($teachers as $teacher)
                <tr>
                    <td><code>{{ $teacher->teacher_code ?? '—' }}</code></td>
                    <td><strong>{{ $teacher->name }}</strong></td>
                    <td>{{ $teacher->gender ? ucfirst($teacher->gender) : '—' }}</td>
                    <td>{{ $teacher->date_of_birth ? $teacher->date_of_birth->format('d M Y') : '—' }}</td>
                    <td>{{ $teacher->phone ?? '—' }}</td>
                    <td>{{ $teacher->email ?? '—' }}</td>
                    <td><span class="badge {{ $teacher->status }}">{{ ucfirst($teacher->status) }}</span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-secondary" onclick="editTeacher({{ json_encode($teacher) }})" data-tip="Edit Teacher">✎</button>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/teachers/{{ $teacher->id }}', 'teacher')" data-tip="Delete Teacher">🗑</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">👨‍🏫</div><h3>No teachers found</h3><p>Add your first teacher.</p><button class="btn btn-primary" onclick="openModal('teacherModal')">+ Add Teacher</button></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($teachers->hasPages())
    <div class="card-footer">
        <div class="pagination-info">Showing {{ $teachers->firstItem() }}–{{ $teachers->lastItem() }} of {{ $teachers->total() }}</div>
        <div class="pagination">@for($i=1;$i<=$teachers->lastPage();$i++)<a href="{{ $teachers->url($i) }}" class="page-btn {{ $teachers->currentPage()==$i?'active':'' }}">{{ $i }}</a>@endfor</div>
    </div>
    @endif
</div>

<div class="modal-overlay" id="teacherModal">
    <div class="modal">
        <div class="modal-header"><h3 id="teacherModalTitle">Add New Teacher</h3><button class="modal-close" onclick="closeModal('teacherModal')">✕</button></div>
        <div class="modal-body">
            <form id="teacherForm" method="POST" action="{{ route('teachers.store') }}">
                @csrf
                <div id="teacherMethodField"></div>
                <div class="form-group">
                    <label class="form-label">Teacher ID</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="teacher_code" id="teacherCodeInput" class="form-control" placeholder="e.g. TCH-A1B2C3" style="flex: 1;">
                        <button type="button" class="btn btn-secondary" onclick="generateTeacherCode()" style="white-space: nowrap;">⚡ Generate</button>
                    </div>
                    <small style="color: var(--text-muted, #94a3b8); margin-top: 4px; display: block;">Leave empty to auto-generate, or type your own unique ID.</small>
                </div>
                <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" id="teacherName" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="teacherDob" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="teacherGender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" id="teacherPhone" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" id="teacherEmail" class="form-control"></div>
                </div>
                <div class="form-group"><label class="form-label">Address</label><input type="text" name="address" id="teacherAddress" class="form-control"></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('teacherModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('teacherForm').submit()">Save Teacher</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { const p = new URLSearchParams(window.location.search); p.set('search', this.value); window.location.search = p.toString(); }
});

function generateTeacherCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'TCH-';
    for (let i = 0; i < 6; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('teacherCodeInput').value = code;
}

function editTeacher(teacher) {
    document.getElementById('teacherModalTitle').innerText = 'Edit Teacher';
    document.getElementById('teacherForm').action = '/teachers/' + teacher.id;
    document.getElementById('teacherMethodField').innerHTML = '@method("PUT")';

    document.getElementById('teacherCodeInput').value = teacher.teacher_code || '';
    document.getElementById('teacherName').value = teacher.name || '';
    document.getElementById('teacherDob').value = teacher.date_of_birth ? teacher.date_of_birth.split('T')[0] : '';
    document.getElementById('teacherGender').value = teacher.gender || '';
    document.getElementById('teacherPhone').value = teacher.phone || '';
    document.getElementById('teacherEmail').value = teacher.email || '';
    document.getElementById('teacherAddress').value = teacher.address || '';

    openModal('teacherModal');
}

// Reset form when opening for "Add"
const originalOpenModal = window.openModal;
window.openModal = function(id) {
    if (id === 'teacherModal' && event && event.target.innerText.includes('+ Add')) {
        document.getElementById('teacherModalTitle').innerText = 'Add New Teacher';
        document.getElementById('teacherForm').action = '{{ route("teachers.store") }}';
        document.getElementById('teacherMethodField').innerHTML = '';
        document.getElementById('teacherForm').reset();
    }
    originalOpenModal(id);
};
</script>
@endpush
@endsection
