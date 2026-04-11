<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — School Register</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
    /* ── Reports dropdown in sidebar ── */
    .nav-group-toggle {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--text-muted, #94a3b8);
        transition: background .15s, color .15s;
        user-select: none;
        gap: 4px;
    }
    .nav-group-toggle:hover {
        background: var(--nav-hover, rgba(255,255,255,.06));
        color: var(--text-primary, #e2e8f0);
    }
    .nav-group-toggle.open {
        color: var(--accent-primary, #818cf8);
        background: rgba(99,102,241,.1);
    }
    .nav-group-toggle .nav-icon { margin-right: 8px; font-size: 15px; }
    .nav-group-toggle .ng-arrow {
        margin-left: auto;
        font-size: 10px;
        transition: transform .22s;
        opacity: .6;
    }
    .nav-group-toggle.open .ng-arrow { transform: rotate(180deg); opacity: 1; }
    .nav-sub {
        display: none;
        padding-left: 14px;
        margin-top: 2px;
    }
    .nav-sub.open { display: block; }
    .nav-sub .nav-item {
        padding-left: 26px;
        font-size: 13px;
        position: relative;
    }
    .nav-sub .nav-item::before {
        content: '';
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 5px; height: 5px;
        border-radius: 50%;
        background: currentColor;
        opacity: .35;
    }
    </style>
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
@php $reportsOpen = request()->routeIs('reports.*'); @endphp
            <div class="nav-group-toggle {{ $reportsOpen ? 'open' : '' }}" id="reportsToggle" onclick="toggleNavGroup('reportsToggle','reportsSub')">
                <span class="nav-icon">📈</span>
                Reports
                <span class="ng-arrow">▼</span>
            </div>
            <div class="nav-sub {{ $reportsOpen ? 'open' : '' }}" id="reportsSub">
                {{-- Payment --}}
                <div style="font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted,#475569);padding:6px 14px 2px 26px;">💰 Payment</div>
                <a href="{{ route('reports.payment.revenue') }}" class="nav-item {{ request()->routeIs('reports.payment.revenue') ? 'active' : '' }}">Revenue</a>
                <a href="{{ route('reports.payment.transactions') }}" class="nav-item {{ request()->routeIs('reports.payment.transactions') ? 'active' : '' }}">Transactions</a>
                <a href="{{ route('reports.payment.overdue') }}" class="nav-item {{ request()->routeIs('reports.payment.overdue') ? 'active' : '' }}" style="{{ request()->routeIs('reports.payment.overdue') ? '' : 'color:#f87171;' }}">⚠️ Overdue</a>
                {{-- Classroom --}}
                <div style="font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted,#475569);padding:6px 14px 2px 26px;">🏫 Classroom</div>
                <a href="{{ route('reports.classroom') }}" class="nav-item {{ request()->routeIs('reports.classroom') ? 'active' : '' }}">Detail</a>
                <a href="{{ route('reports.classroom.summary') }}" class="nav-item {{ request()->routeIs('reports.classroom.summary') ? 'active' : '' }}">Summary</a>
                <a href="{{ route('reports.classroom.unpaid') }}" class="nav-item {{ request()->routeIs('reports.classroom.unpaid') ? 'active' : '' }}">Not Paid</a>
                {{-- Students --}}
                <div style="font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted,#475569);padding:6px 14px 2px 26px;">🎓 Students</div>
                <a href="{{ route('reports.students') }}" class="nav-item {{ request()->routeIs('reports.students*') ? 'active' : '' }}">Student Report</a>
                {{-- Term/Grade --}}
                <div style="font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted,#475569);padding:6px 14px 2px 26px;">📚 Term / Grade</div>
                <a href="{{ route('reports.term-grade') }}" class="nav-item {{ request()->routeIs('reports.term-grade*') ? 'active' : '' }}">Term &amp; Grade</a>
                {{-- Teachers --}}
                <div style="font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text-muted,#475569);padding:6px 14px 2px 26px;">👩‍🏫 Teachers</div>
                <a href="{{ route('reports.teachers') }}" class="nav-item {{ request()->routeIs('reports.teachers*') ? 'active' : '' }}">Teacher Report</a>
            </div>
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
<script>
function toggleNavGroup(toggleId, subId) {
    const toggle = document.getElementById(toggleId);
    const sub    = document.getElementById(subId);
    toggle.classList.toggle('open');
    sub.classList.toggle('open');
}
</script>
@stack('scripts')
</body>
</html>
