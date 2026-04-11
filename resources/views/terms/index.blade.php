@extends('layouts.app')
@section('title', 'Terms')
@section('page-title', 'Academic Terms')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>All Terms</h2>
        <button class="btn btn-primary" onclick="openAddTermModal()">+ Add Term</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Start Date') }}</th>
                    <th>{{ __('End Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($terms as $term)
                <tr>
                    <td><strong>{{ $term->name }}</strong></td>
                    <td>{{ $term->start_date->format('d M Y') }}</td>
                    <td>{{ $term->end_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $term->status }}">{{ ucfirst($term->status) }}</span></td>
                    <td>
                        <div class="btn-group">
                            @if(auth()->user()->role !== 'accountant')
                            <button class="btn btn-sm btn-secondary"
                                data-tip="Edit Term"
                                onclick="editTerm(
                                    {{ $term->id }},
                                    '{{ addslashes($term->name) }}',
                                    '{{ $term->start_date->format('Y-m-d') }}',
                                    '{{ $term->end_date->format('Y-m-d') }}',
                                    '{{ $term->status }}'
                                )"><i data-lucide="pencil" style="width:14px;height:14px;"></i></button>
                            @endif
                            @if(auth()->user()->role !== 'accountant')
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/terms/{{ $term->id }}', 'term')" data-tip="Delete Term"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon"><i data-lucide="calendar-range" style="width:40px;height:40px;"></i></div>
                            <h3>No terms yet</h3>
                            <p>Add your first academic term.</p>
                            <button class="btn btn-primary" onclick="openAddTermModal()">+ Add Term</button>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add / Edit Term Modal --}}
<div class="modal-overlay" id="termModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="termModalTitle">Add Term</h3>
            <button class="modal-close" onclick="closeModal('termModal')"><i data-lucide="x" style="width:18px;height:18px;"></i></button>
        </div>
        <div class="modal-body">
            <form id="termForm" method="POST" action="{{ route('terms.store') }}">
                @csrf
                <input type="hidden" name="_method" id="termMethod" value="POST">

                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" id="termName" class="form-control" placeholder="e.g. 2025-2026" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Date *</label>
                        <input type="date" name="start_date" id="termStartDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date *</label>
                        <input type="date" name="end_date" id="termEndDate" class="form-control" required>
                    </div>
                </div>
                <div class="form-group" id="statusGroup" style="display:none;">
                    <label class="form-label">Status</label>
                    <select name="status" id="termStatus" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('termModal')">Cancel</button>
            <button class="btn btn-primary" id="termSaveBtn" onclick="document.getElementById('termForm').submit()">Save</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAddTermModal() {
    document.getElementById('termModalTitle').innerText = 'Add Term';
    document.getElementById('termForm').action = "{{ route('terms.store') }}";
    document.getElementById('termMethod').value = 'POST';
    document.getElementById('termForm').reset();
    document.getElementById('statusGroup').style.display = 'none';
    document.getElementById('termSaveBtn').innerText = 'Save';
    openModal('termModal');
}

function editTerm(id, name, startDate, endDate, status) {
    document.getElementById('termModalTitle').innerText = 'Edit Term';
    document.getElementById('termForm').action = `/terms/${id}`;
    document.getElementById('termMethod').value = 'PUT';

    document.getElementById('termName').value      = name;
    document.getElementById('termStartDate').value = startDate;
    document.getElementById('termEndDate').value   = endDate;
    document.getElementById('termStatus').value    = status;

    // Show status field only in edit mode
    document.getElementById('statusGroup').style.display = 'block';
    document.getElementById('termSaveBtn').innerText = 'Update';

    openModal('termModal');
}
</script>
@endpush
@endsection
