@extends('layouts.app')
@section('title', 'Teachers')
@section('page-title', 'Teachers')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="toolbar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Search teachers..." value="{{ request('search') }}">
            </div>
        </div>
        <button class="btn btn-primary" onclick="openModal('teacherModal')">+ Add Teacher</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Name</th><th>Phone</th><th>Email</th><th>Classroom</th><th>Status</th><th>Hire Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($teachers as $teacher)
                <tr>
                    <td><strong>{{ $teacher->name }}</strong></td>
                    <td>{{ $teacher->phone ?? '—' }}</td>
                    <td>{{ $teacher->email ?? '—' }}</td>
                    <td>{{ $teacher->classroom->name ?? '—' }}</td>
                    <td><span class="badge {{ $teacher->status }}">{{ ucfirst($teacher->status) }}</span></td>
                    <td>{{ $teacher->hire_date ? $teacher->hire_date->format('d M Y') : '—' }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/teachers/{{ $teacher->id }}', 'teacher')">🗑</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="empty-icon">👨‍🏫</div><h3>No teachers found</h3><p>Add your first teacher.</p><button class="btn btn-primary" onclick="openModal('teacherModal')">+ Add Teacher</button></div></td></tr>
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
        <div class="modal-header"><h3>Add New Teacher</h3><button class="modal-close" onclick="closeModal('teacherModal')">✕</button></div>
        <div class="modal-body">
            <form id="teacherForm" method="POST" action="{{ route('teachers.store') }}">
                @csrf
                <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Classroom</label><select name="classroom_id" class="form-control"><option value="">Select</option>@foreach($classrooms as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label class="form-label">Hire Date</label><input type="date" name="hire_date" class="form-control"></div>
                </div>
                <div class="form-group"><label class="form-label">Address</label><input type="text" name="address" class="form-control"></div>
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
</script>
@endpush
@endsection
