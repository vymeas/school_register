@extends('layouts.app')
@section('title', __('Users'))
@section('page-title', __('Users'))

@section('content')
<div class="card">
    <div class="card-header">
        <div class="toolbar">
            <div class="search-box">
                <span class="search-icon"><i data-lucide="search" style="width:14px;height:14px;"></i></span>
                <input type="text" id="searchInput" placeholder="Search users..." value="{{ request('search') }}">
            </div>
            <select class="form-control" style="width:auto;" id="roleFilter">
                <option value="">All Roles</option>
                @if(auth()->user()->role === 'super_admin')
                    <option value="super_admin" {{ request('role')=='super_admin'?'selected':'' }}>Super Admin</option>
                @endif
                <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                <option value="accountant" {{ request('role')=='accountant'?'selected':'' }}>Accountant</option>
            </select>
        </div>
        <button class="btn btn-primary" onclick="openModal('userModal')">+ Add User</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>{{ __('Username') }}</th><th>{{ __('Full Name') }}</th><th>{{ __('Role') }}</th><th>{{ __('Email') }}</th><th>{{ __('Phone') }}</th><th>{{ __('Status') }}</th><th>{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->username }}</strong></td>
                    <td>{{ $user->full_name }}</td>
                    <td><span class="badge info">{{ str_replace('_', ' ', ucfirst($user->role)) }}</span></td>
                    <td>{{ $user->email ?? '—' }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td><span class="badge {{ $user->status }}">{{ ucfirst($user->status) }}</span></td>
                    <td><div class="btn-group">
                        @if(auth()->user()->role !== 'accountant')
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/users/{{ $user->id }}', 'user')" data-tip="Delete User"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                        @endif
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i data-lucide="users"></i></div><h3>No users</h3></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="userModal">
    <div class="modal">
        <div class="modal-header"><h3>Add User</h3><button class="modal-close" onclick="closeModal('userModal')"><i data-lucide="x" style="width:18px;height:18px;"></i></button></div>
        <div class="modal-body">
            <form id="userForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Username *</label><input type="text" name="username" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
                </div>
                <div class="form-group"><label class="form-label">Full Name *</label><input type="text" name="full_name" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-control" required>
                            @if(auth()->user()->role === 'super_admin')
                                <option value="super_admin">Super Admin</option>
                            @endif
                            <option value="admin">Admin</option>
                            <option value="accountant">Accountant</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                </div>
                <div class="form-group"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('userModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('userForm').submit()">Save User</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { const p = new URLSearchParams(window.location.search); p.set('search', this.value); window.location.search = p.toString(); }
});
document.getElementById('roleFilter').addEventListener('change', function() {
    const p = new URLSearchParams(window.location.search);
    if (this.value) p.set('role', this.value);
    else p.delete('role');
    window.location.search = p.toString();
});
</script>
@endpush
@endsection
