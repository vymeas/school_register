<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — School Register</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app-wrapper">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">SR</div>
            <div>
                <div class="brand-text">School Register</div>
                <div class="brand-sub">Admin Panel</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Dashboard
            </a>

            <div class="nav-section-title">Management</div>
            <a href="{{ route('students.index') }}" class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <span class="nav-icon">🎓</span> Students
            </a>
            <a href="{{ route('teachers.index') }}" class="nav-item {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                <span class="nav-icon">👨‍🏫</span> Teachers
            </a>
            <a href="{{ route('classrooms.index') }}" class="nav-item {{ request()->routeIs('classrooms.index') || request()->routeIs('classrooms.store') ? 'active' : '' }}">
                <span class="nav-icon">🏫</span> Classrooms
            </a>
            <a href="{{ route('grades.index') }}" class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                <span class="nav-icon">📚</span> Grades
            </a>
            <a href="{{ route('terms.index') }}" class="nav-item {{ request()->routeIs('terms.*') ? 'active' : '' }}">
                <span class="nav-icon">📅</span> Terms
            </a>
            <a href="{{ route('turns.index') }}" class="nav-item {{ request()->routeIs('turns.*') ? 'active' : '' }}">
                <span class="nav-icon">🕒</span> Turns
            </a>

            <div class="nav-section-title">Finance</div>
            <a href="{{ route('tuition-plans.index') }}" class="nav-item {{ request()->routeIs('tuition-plans.*') ? 'active' : '' }}">
                <span class="nav-icon">💰</span> Tuition Plans
            </a>
            <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <span class="nav-icon">💳</span> Payments
            </a>
            <a href="{{ route('enrollments.index') }}" class="nav-item {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                <span class="nav-icon">📝</span> Enrollments
            </a>

            <div class="nav-section-title">System</div>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Reports
            </a>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span> Users
            </a>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                <span class="nav-icon">⚙️</span> Settings
            </a>
            <a href="{{ route('settings.grades.restore') }}" class="nav-item {{ request()->routeIs('settings.grades.restore') ? 'active' : '' }}">
                <span class="nav-icon">♻️</span> Restore Grades
            </a>
            <a href="{{ route('settings.teachers.restore') }}" class="nav-item {{ request()->routeIs('settings.teachers.restore') ? 'active' : '' }}">
                <span class="nav-icon">♻️</span> Restore Teachers
            </a>
            <a href="{{ route('settings.students.restore') }}" class="nav-item {{ request()->routeIs('settings.students.restore') ? 'active' : '' }}">
                <span class="nav-icon">♻️</span> Restore Students
            </a>
            <a href="{{ route('classrooms.archived') }}" class="nav-item {{ request()->routeIs('classrooms.archived') ? 'active' : '' }}">
                <span class="nav-icon">📦</span> Restore Classrooms
            </a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-btn" id="sidebar-toggle">☰</button>
                <div>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <div class="breadcrumb">
                        <span>Home</span> / <span>@yield('page-title', 'Dashboard')</span>
                    </div>
                </div>
            </div>
            <div class="topbar-right">
                <div class="user-menu">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'A', 0, 2)) }}</div>
                    <div class="user-info">
                        <div class="name">{{ auth()->user()->full_name ?? 'Admin' }}</div>
                        <div class="role">{{ str_replace('_', ' ', auth()->user()->role ?? 'admin') }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="topbar-btn" title="Logout">🚪</button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">✕ {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
