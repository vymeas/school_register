<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — School Register</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- Lucide Icons — #1 Modern Icon Library 2026 --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
    @font-face {
        font-family: 'Moul';
        src: url("{{ asset('fonts/Moul-Regular.ttf') }}") format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    .brand-text { font-family: 'Moul', cursive; font-size: 16px; line-height: 1.4; color: #1e293b; }

    /* ── Sidebar Dropdown (Reports etc.) ── */
    .nav-group-toggle {
        display: flex;
        align-items: center;
        padding: 11px 14px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-sidebar);
        transition: background .15s, color .15s;
        user-select: none;
        gap: 4px;
    }
    .nav-group-toggle:hover {
        background: var(--accent-primary-light, rgba(60,80,224,0.08));
        color: var(--accent-primary, #3c50e0);
    }
    .nav-group-toggle.open {
        color: var(--accent-primary, #3c50e0);
        background: var(--accent-primary-light, rgba(60,80,224,0.08));
    }
    .nav-group-toggle .nav-icon { margin-right: 6px; }
    .nav-group-toggle .ng-arrow {
        margin-left: auto;
        transition: transform .22s;
        opacity: .5;
    }
    .nav-group-toggle.open .ng-arrow { transform: rotate(180deg); opacity: .8; }
    .nav-sub {
        display: none;
        padding-left: 14px;
        margin-top: 2px;
    }
    .nav-sub.open { display: block; }
    .nav-sub .nav-item {
        padding-left: 26px;
        font-size: 13.5px;
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
        opacity: .3;
    }
    .nav-sub .nav-item.active::before { opacity: .8; }

    /* ── Sidebar sub-section labels ── */
    .nav-sub-label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .6px;
        text-transform: uppercase;
        color: var(--text-sidebar-heading, #94a3b8);
        padding: 8px 14px 2px 26px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ── User dropdown ── */
    .user-dropdown {
        position: relative;
    }
    .user-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        width: 200px;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-4px);
        transition: all .15s ease;
        z-index: 200;
    }
    .user-dropdown.open .user-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .user-dropdown-menu a,
    .user-dropdown-menu button {
        display: flex;
        width: 100%;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
        background: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: all .12s;
        text-align: left;
    }
    .user-dropdown-menu a:hover,
    .user-dropdown-menu button:hover {
        background: var(--bg-body);
        color: var(--text-primary);
    }
    .user-dropdown-divider {
        height: 1px;
        background: var(--border-color);
        margin: 4px 0;
    }

    /* Overlay when sidebar open on mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 99;
    }
    @media (max-width: 1024px) {
        .sidebar-overlay.show { display: block; }
    }

    /* ── Lucide icon sizing in nav ── */
    .nav-icon { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; flex-shrink: 0; }
    .nav-icon svg, .nav-icon i[data-lucide] { width: 18px; height: 18px; }
    .nav-sub-label i[data-lucide] { width: 13px; height: 13px; }

    /* Language Switcher */
    .lang-switcher {
        display: flex; gap: 8px; margin-right: 16px;
        background: var(--bg-card); padding: 4px; border-radius: 8px;
        border: 1px solid var(--border-color);
    }
    .lang-item {
        padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800;
        cursor: pointer; text-decoration: none; color: var(--text-muted);
        transition: all 0.2s;
    }
    .lang-item.active { background: var(--accent-primary); color: #fff; }
    .lang-item:hover:not(.active) { background: var(--bg-body); color: var(--text-primary); }
    </style>
    @stack('styles')
</head>
<body>
<div class="app-wrapper">
    {{-- Mobile overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="brand-logo" style="height: 45px; width: auto; object-fit: contain; margin-right: 12px; border-radius: 8px;">
            <div>
                <div class="brand-text">សាលារៀនវិទូជន</div>
                <div class="brand-sub">Admin Panel</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">{{ __('Menu') }}</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="layout-dashboard"></i></span> <span>{{ __('Dashboard') }}</span>
            </a>

            <div class="nav-section-title">{{ __('Management') }}</div>
            <a href="{{ route('students.index') }}" class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="graduation-cap"></i></span> <span>{{ __('Students') }}</span>
            </a>
            <a href="{{ route('teachers.index') }}" class="nav-item {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="user-round-check"></i></span> <span>{{ __('Teachers') }}</span>
            </a>
            <a href="{{ route('classrooms.index') }}" class="nav-item {{ request()->routeIs('classrooms.index') || request()->routeIs('classrooms.store') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="school"></i></span> <span>{{ __('Classrooms') }}</span>
            </a>
            <a href="{{ route('grades.index') }}" class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="book-open"></i></span> <span>{{ __('Grades') }}</span>
            </a>
            <a href="{{ route('terms.index') }}" class="nav-item {{ request()->routeIs('terms.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="calendar-range"></i></span> <span>{{ __('Terms') }}</span>
            </a>
            <a href="{{ route('turns.index') }}" class="nav-item {{ request()->routeIs('turns.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="clock-4"></i></span> <span>{{ __('Turns') }}</span>
            </a>

            <div class="nav-section-title">{{ __('Finance') }}</div>
            @if(auth()->user()->role !== 'accountant')
            <a href="{{ route('tuition-plans.index') }}" class="nav-item {{ request()->routeIs('tuition-plans.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="banknote"></i></span> <span>{{ __('Tuition Plans') }}</span>
            </a>
            @endif
            <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="credit-card"></i></span> <span>{{ __('Payments') }}</span>
            </a>
            <a href="{{ route('enrollments.index') }}" class="nav-item {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="clipboard-list"></i></span> <span>{{ __('Enrollments') }}</span>
            </a>

            <div class="nav-section-title">{{ __('Reports') }}</div>
            
            {{-- Payment Reports --}}
            @php $payReportsOpen = request()->routeIs('reports.payment.*'); @endphp
            <div class="nav-group-toggle {{ $payReportsOpen ? 'open' : '' }}" id="payReportsToggle" onclick="toggleNavGroup('payReportsToggle','payReportsSub')">
                <span class="nav-icon"><i data-lucide="wallet"></i></span>
                <span>{{ __('Payment Rep') }}</span>
                <span class="ng-arrow"><i data-lucide="chevron-down" style="width:14px;height:14px;"></i></span>
            </div>
            <div class="nav-sub {{ $payReportsOpen ? 'open' : '' }}" id="payReportsSub">
                <a href="{{ route('reports.payment.revenue') }}" class="nav-item {{ request()->routeIs('reports.payment.revenue') ? 'active' : '' }}">Revenue</a>
                <a href="{{ route('reports.payment.transactions') }}" class="nav-item {{ request()->routeIs('reports.payment.transactions') ? 'active' : '' }}">Transactions</a>
                <a href="{{ route('reports.payment.overdue') }}" class="nav-item {{ request()->routeIs('reports.payment.overdue') ? 'active' : '' }}">Overdue</a>
            </div>

            {{-- Classroom Reports --}}
            @php $classReportsOpen = request()->routeIs('reports.classroom*'); @endphp
            <div class="nav-group-toggle {{ $classReportsOpen ? 'open' : '' }}" id="classReportsToggle" onclick="toggleNavGroup('classReportsToggle','classReportsSub')">
                <span class="nav-icon"><i data-lucide="school"></i></span>
                <span>{{ __('Classroom Rep') }}</span>
                <span class="ng-arrow"><i data-lucide="chevron-down" style="width:14px;height:14px;"></i></span>
            </div>
            <div class="nav-sub {{ $classReportsOpen ? 'open' : '' }}" id="classReportsSub">
                <a href="{{ route('reports.classroom') }}" class="nav-item {{ request()->routeIs('reports.classroom') ? 'active' : '' }}">Detail</a>
                <a href="{{ route('reports.classroom.summary') }}" class="nav-item {{ request()->routeIs('reports.classroom.summary') ? 'active' : '' }}">Summary</a>
                <a href="{{ route('reports.classroom.unpaid') }}" class="nav-item {{ request()->routeIs('reports.classroom.unpaid') ? 'active' : '' }}">Not Paid</a>
            </div>

            {{-- Student Reports --}}
            @php $studReportsOpen = request()->routeIs('reports.students*'); @endphp
            <div class="nav-group-toggle {{ $studReportsOpen ? 'open' : '' }}" id="studReportsToggle" onclick="toggleNavGroup('studReportsToggle','studReportsSub')">
                <span class="nav-icon"><i data-lucide="graduation-cap"></i></span>
                <span>{{ __('Student Rep') }}</span>
                <span class="ng-arrow"><i data-lucide="chevron-down" style="width:14px;height:14px;"></i></span>
            </div>
            <div class="nav-sub {{ $studReportsOpen ? 'open' : '' }}" id="studReportsSub">
                <a href="{{ route('reports.students') }}" class="nav-item {{ request()->routeIs('reports.students*') ? 'active' : '' }}">Student Report</a>
            </div>

            {{-- Term / Grade Reports --}}
            @php $tgReportsOpen = request()->routeIs('reports.term-grade*'); @endphp
            <div class="nav-group-toggle {{ $tgReportsOpen ? 'open' : '' }}" id="tgReportsToggle" onclick="toggleNavGroup('tgReportsToggle','tgReportsSub')">
                <span class="nav-icon"><i data-lucide="book-open"></i></span>
                <span>{{ __('Term / Grade') }}</span>
                <span class="ng-arrow"><i data-lucide="chevron-down" style="width:14px;height:14px;"></i></span>
            </div>
            <div class="nav-sub {{ $tgReportsOpen ? 'open' : '' }}" id="tgReportsSub">
                <a href="{{ route('reports.term-grade') }}" class="nav-item {{ request()->routeIs('reports.term-grade*') ? 'active' : '' }}">Term &amp; Grade</a>
            </div>

            {{-- Teacher Reports --}}
            @php $teachReportsOpen = request()->routeIs('reports.teachers*'); @endphp
            <div class="nav-group-toggle {{ $teachReportsOpen ? 'open' : '' }}" id="teachReportsToggle" onclick="toggleNavGroup('teachReportsToggle','teachReportsSub')">
                <span class="nav-icon"><i data-lucide="user-round-check"></i></span>
                <span>{{ __('Teacher Rep') }}</span>
                <span class="ng-arrow"><i data-lucide="chevron-down" style="width:14px;height:14px;"></i></span>
            </div>
            <div class="nav-sub {{ $teachReportsOpen ? 'open' : '' }}" id="teachReportsSub">
                <a href="{{ route('reports.teachers') }}" class="nav-item {{ request()->routeIs('reports.teachers*') ? 'active' : '' }}">Teacher Report</a>
            </div>

            <div class="nav-section-title">{{ __('System') }}</div>
            @if(auth()->user()->role !== 'accountant')
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="users"></i></span> <span>{{ __('Users') }}</span>
            </a>
            @endif
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="settings"></i></span> <span>{{ __('Settings') }}</span>
            </a>
            @if(!in_array(auth()->user()->role, ['accountant', 'admin']))
            <a href="{{ route('settings.grades.restore') }}" class="nav-item {{ request()->routeIs('settings.grades.restore') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="rotate-ccw"></i></span> <span>Restore Grades</span>
            </a>
            <a href="{{ route('settings.teachers.restore') }}" class="nav-item {{ request()->routeIs('settings.teachers.restore') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="rotate-ccw"></i></span> <span>Restore Teachers</span>
            </a>
            <a href="{{ route('settings.students.restore') }}" class="nav-item {{ request()->routeIs('settings.students.restore') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="rotate-ccw"></i></span> <span>Restore Students</span>
            </a>
            <a href="{{ route('classrooms.archived') }}" class="nav-item {{ request()->routeIs('classrooms.archived') ? 'active' : '' }}">
                <span class="nav-icon"><i data-lucide="archive"></i></span> <span>Restore Classrooms</span>
            </a>
            @endif
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-btn" id="sidebar-toggle" onclick="toggleSidebar()">
                    <i data-lucide="menu" style="width:18px;height:18px;"></i>
                </button>
                <div>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <div class="breadcrumb">
                        <span>Home</span>
                        <i data-lucide="chevron-right" style="width:14px;height:14px;"></i>
                        <span>@yield('page-title', 'Dashboard')</span>
                    </div>
                </div>
            </div>
            <div class="topbar-right">
                {{-- Language Switcher --}}
                <div class="lang-switcher">
                    <a href="{{ route('lang.switch', 'en') }}" class="lang-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                    <a href="{{ route('lang.switch', 'km') }}" class="lang-item {{ app()->getLocale() == 'km' ? 'active' : '' }}">KM</a>
                </div>

                {{-- User Dropdown --}}
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-menu" onclick="document.getElementById('userDropdown').classList.toggle('open')">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'A', 0, 2)) }}</div>
                        <div class="user-info">
                            <div class="name">{{ auth()->user()->full_name ?? 'Admin' }}</div>
                            <div class="role">{{ str_replace('_', ' ', auth()->user()->role ?? 'admin') }}</div>
                        </div>
                        <i data-lucide="chevron-down" style="width:14px;height:14px;color:var(--text-muted);margin-left:4px;"></i>
                    </div>
                    <div class="user-dropdown-menu">
                        <a href="{{ route('settings.index') }}"><i data-lucide="settings" style="width:16px;height:16px;"></i> Account Settings</a>
                        <div class="user-dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST" style="margin:0">
                            @csrf
                            <button type="submit"><i data-lucide="log-out" style="width:16px;height:16px;"></i> Sign Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i data-lucide="check-circle-2" style="width:18px;height:18px;flex-shrink:0;"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
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
// Initialize Lucide icons
lucide.createIcons();

function toggleNavGroup(toggleId, subId) {
    const toggle = document.getElementById(toggleId);
    const sub    = document.getElementById(subId);
    toggle.classList.toggle('open');
    sub.classList.toggle('open');
}

function toggleSidebar() {
    const wrapper = document.querySelector('.app-wrapper');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (window.innerWidth <= 1024) {
        // Mobile behavior: Slide in/out
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    } else {
        // Desktop behavior: Collapse/Expand
        wrapper.classList.toggle('sidebar-collapsed');
    }
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
}

// Close user dropdown on outside click
document.addEventListener('click', function(e) {
    const ud = document.getElementById('userDropdown');
    if (ud && !ud.contains(e.target)) ud.classList.remove('open');
});
</script>
@stack('scripts')
<script>
// Re-initialize Lucide after stacked scripts load new icons
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
