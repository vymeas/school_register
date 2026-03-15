@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="settings-section">
            <h3>🏫 School Information</h3>
            <div class="form-row">
                <div class="form-group"><label class="form-label">School Name</label><input type="text" class="form-control" value="School Register" readonly></div>
                <div class="form-group"><label class="form-label">Address</label><input type="text" class="form-control" placeholder="Enter school address"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Phone</label><input type="text" class="form-control" placeholder="Enter phone"></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" placeholder="Enter email"></div>
            </div>
        </div>

        <div class="settings-section">
            <h3>👤 Account</h3>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Username</label><input type="text" class="form-control" value="{{ auth()->user()->username ?? '' }}" readonly></div>
                <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-control" value="{{ auth()->user()->full_name ?? '' }}" readonly></div>
            </div>
            <div class="form-group"><label class="form-label">Role</label><input type="text" class="form-control" value="{{ str_replace('_', ' ', ucfirst(auth()->user()->role ?? '' )) }}" readonly></div>
        </div>

        <div class="settings-section">
            <h3>🔧 System</h3>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Laravel Version</label><input type="text" class="form-control" value="{{ app()->version() }}" readonly></div>
                <div class="form-group"><label class="form-label">PHP Version</label><input type="text" class="form-control" value="{{ phpversion() }}" readonly></div>
            </div>
        </div>
    </div>
</div>
@endsection
