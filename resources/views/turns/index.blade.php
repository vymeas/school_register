@extends('layouts.app')
@section('title', __('Turns'))
@section('page-title', __('Turns'))

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Study Turns (Time Slots)</h2>
        <button class="btn btn-primary" onclick="openAddTurnModal()">+ Add Turn</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Start Time') }}</th>
                    <th>{{ __('End Time') }}</th>
                    <th>{{ __('Classrooms') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($turns as $turn)
                <tr>
                    <td><strong>{{ $turn->name }}</strong></td>
                    <td>{{ date('h:i A', strtotime($turn->start_time)) }}</td>
                    <td>{{ date('h:i A', strtotime($turn->end_time)) }}</td>
                    <td><span class="badge info">{{ $turn->classrooms_count }}</span></td>
                    <td>
                        <div class="btn-group">
                            @if(auth()->user()->role !== 'accountant')
                            <button class="btn btn-sm btn-secondary" onclick="editTurn({{ $turn->id }}, '{{ $turn->name }}', '{{ $turn->start_time }}', '{{ $turn->end_time }}')" data-tip="Edit Turn"><i data-lucide="pencil" style="width:14px;height:14px;"></i></button>
                            @endif
                            @if(auth()->user()->role !== 'accountant')
                            <form action="{{ route('turns.destroy', $turn) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" data-tip="Delete Turn"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon"><i data-lucide="clock-4" style="width:40px;height:40px;"></i></div>
                            <h3>No turns defined</h3>
                            <p>Add study time slots (e.g. Morning, Afternoon) for classrooms.</p>
                            <button class="btn btn-primary" onclick="openAddTurnModal()">+ Add Turn</button>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal-overlay" id="turnModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="turnModalTitle">Add Turn</h3>
            <button class="modal-close" onclick="closeModal('turnModal')"><i data-lucide="x" style="width:18px;height:18px;"></i></button>
        </div>
        <div class="modal-body">
            <form id="turnForm" method="POST" action="{{ route('turns.store') }}">
                @csrf
                <input type="hidden" name="_method" id="turnFormMethod" value="POST">
                
                <div class="form-group">
                    <label class="form-label">Turn Name *</label>
                    <input type="text" name="name" id="turnName" class="form-control" placeholder="e.g. Morning, Afternoon, Full Day" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Time *</label>
                        <input type="time" name="start_time" id="turnStartTime" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Time *</label>
                        <input type="time" name="end_time" id="turnEndTime" class="form-control" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('turnModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('turnForm').submit()">Save Turn</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAddTurnModal() {
    document.getElementById('turnModalTitle').innerText = 'Add Turn';
    document.getElementById('turnForm').action = "{{ route('turns.store') }}";
    document.getElementById('turnFormMethod').value = 'POST';
    document.getElementById('turnForm').reset();
    openModal('turnModal');
}

function editTurn(id, name, startTime, endTime) {
    document.getElementById('turnModalTitle').innerText = 'Edit Turn';
    document.getElementById('turnForm').action = `/turns/${id}`;
    document.getElementById('turnFormMethod').value = 'PUT';
    
    document.getElementById('turnName').value = name;
    document.getElementById('turnStartTime').value = startTime;
    document.getElementById('turnEndTime').value = endTime;
    
    openModal('turnModal');
}
</script>
@endpush
@endsection
