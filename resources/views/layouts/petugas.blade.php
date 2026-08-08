<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Petugas') - TENGGANGLOPI</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #0f172a;
            --text-muted: #64748b;
            --accent: #2563eb;
            --accent-hover: #1d4ed8;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;

            /* Sensor Vars (Cerah - Default) */
            --sensor-bg: #ffffff;
            --sensor-border: #e2e8f0;
            --sensor-title: #475569;
            --sensor-val: #1e293b;
            --sensor-status-bg: #f1f5f9;
            --sensor-status-border: #e2e8f0;
            --sensor-status-text: #64748b;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
        
        /* Theme Engine Script (Must be in head to prevent flickering) */
        <script>
            (function() {
                const theme = localStorage.getItem('tengganglopi_theme') || 'cerah';
                const root = document.documentElement;
                if(theme === 'gelap') {
                    root.style.setProperty('--bg-main', '#13151a');
                    root.style.setProperty('--bg-sidebar', '#1a1c23');
                    root.style.setProperty('--bg-card', '#1a1c23');
                    root.style.setProperty('--border-color', '#2d303b');
                    root.style.setProperty('--text-primary', '#ffffff');
                    root.style.setProperty('--text-muted', '#8b949e');
                    
                    root.style.setProperty('--sensor-bg', '#1e2029');
                    root.style.setProperty('--sensor-border', '#2d303b');
                    root.style.setProperty('--sensor-title', '#9ca3af');
                    root.style.setProperty('--sensor-val', '#f8fafc');
                    root.style.setProperty('--sensor-status-bg', '#13151a');
                    root.style.setProperty('--sensor-status-border', '#2d303b');
                    root.style.setProperty('--sensor-status-text', '#9ca3af');
                } else if(theme === 'biru') {
                    root.style.setProperty('--bg-main', '#0f172a');
                    root.style.setProperty('--bg-sidebar', '#1e293b');
                    root.style.setProperty('--bg-card', '#1e293b');
                    root.style.setProperty('--border-color', '#334155');
                    root.style.setProperty('--text-primary', '#f8fafc');
                    root.style.setProperty('--text-muted', '#94a3b8');
                    root.style.setProperty('--accent', '#0ea5e9');
                    root.style.setProperty('--accent-hover', '#0284c7');
                    
                    root.style.setProperty('--sensor-bg', '#1e293b');
                    root.style.setProperty('--sensor-border', '#334155');
                    root.style.setProperty('--sensor-title', '#94a3b8');
                    root.style.setProperty('--sensor-val', '#f8fafc');
                    root.style.setProperty('--sensor-status-bg', '#0f172a');
                    root.style.setProperty('--sensor-status-border', '#334155');
                    root.style.setProperty('--sensor-status-text', '#cbd5e1');
                }
            })();
        </script>
        
        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling (Kodezi-inspired) */
        .sidebar {
            width: 270px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 20px 20px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-wrap img {
            width: 28px; height: 28px; border-radius: 6px;
        }
        .logo-wrap h2 {
            font-size: 16px; font-weight: 700; color: var(--text-primary);
            letter-spacing: 0.5px;
        }
        .btn-collapse {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
        }
        .btn-collapse:hover { color: var(--text-primary); }

        /* Search Box */
        .sidebar-search {
            padding: 10px 20px 20px;
        }
        .search-box {
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            display: flex;
            align-items: center;
            padding: 8px 12px;
            gap: 8px;
        }
        .search-box svg { color: var(--text-muted); }
        .search-box input {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 13px;
            outline: none;
        }
        .search-box input::placeholder { color: var(--text-muted); }
        .search-box .shortcut {
            background: var(--border-color);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Nav Menu */
        .nav-menu {
            padding: 0 15px;
            flex: 1;
            overflow-y: auto;
        }
        /* Custom scrollbar for nav-menu */
        .nav-menu::-webkit-scrollbar { width: 4px; }
        .nav-menu::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
        
        .nav-group-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 8px 10px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.2s;
        }
        .nav-item svg { width: 16px; height: 16px; opacity: 0.8; }
        
        .nav-item:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--accent);
        }
        .nav-item.active {
            background: rgba(37, 99, 235, 0.1);
            color: var(--accent);
            position: relative;
        }
        
        /* Sidebar Footer (Profile) */
        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .user-profile-sidebar {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }
        .user-profile-sidebar img {
            width: 32px; height: 32px; border-radius: 50%;
            border: 1px solid var(--border-color);
        }
        .user-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .user-info .name {
            font-size: 13px; font-weight: 600; color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-info .email {
            font-size: 11px; color: var(--text-muted);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        
        .btn-logout-icon {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: 0.2s;
        }
        .btn-logout-icon:hover { color: var(--danger); background: rgba(248, 113, 113, 0.1); }
        
        /* Main Content Styling */
        .main-content {
            flex: 1;
            margin-left: 270px;
            padding: 35px 45px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }
        .topbar-left {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .topbar h1 {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        .topbar p {
            color: var(--success);
            font-size: 13px;
            margin: 5px 0 0 0;
            font-weight: 500;
        }
        
        .topbar-clock-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            min-width: 320px;
        }
        .topbar-clock-card .date-text {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .topbar-clock-card .time-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .topbar-clock-card .local-time, .topbar-clock-card .utc-time {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .topbar-clock-card .local-time span, .topbar-clock-card .utc-time span {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            font-variant-numeric: tabular-nums;
            letter-spacing: 1px;
        }
        .topbar-clock-card small {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
        }
        .topbar-clock-card .time-divider {
            color: var(--border-color);
            font-size: 18px;
            font-weight: 300;
        }
        .role-badge {
            background: rgba(59, 130, 246, 0.15);
            color: var(--accent);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 24px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .stat-card .title {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
        }
        .stat-card .trend {
            font-size: 12px;
            font-weight: 500;
        }

        /* Panels */
        .panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .panel-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }
        .status-item .boat-info h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .status-item .boat-info p {
            font-size: 12px;
            color: var(--text-muted);
        }
        
        /* Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge.safe { background: rgba(52, 211, 153, 0.1); color: var(--success); }
        .badge.danger { background: rgba(248, 113, 113, 0.1); color: var(--danger); }
        .badge.warning { background: rgba(251, 191, 36, 0.1); color: var(--warning); }

        /* Sidebar Blue Theme Override */
        .sidebar { background: #1d4ed8 !important; border-right: none !important; }
        .sidebar .logo-wrap h2, .sidebar .user-info .name, .sidebar .nav-item.active { color: #ffffff !important; }
        .sidebar .btn-collapse, .sidebar .nav-item, .sidebar .user-info .email, .sidebar .btn-logout-icon, .sidebar .nav-group-title { color: rgba(255, 255, 255, 0.7) !important; }
        .sidebar .btn-collapse:hover, .sidebar .nav-item:hover, .sidebar .btn-logout-icon:hover { color: #ffffff !important; }
        .sidebar .nav-item:hover, .sidebar .btn-logout-icon:hover { background: rgba(255, 255, 255, 0.1) !important; }
        .sidebar .nav-item.active { background: rgba(255, 255, 255, 0.2) !important; }
        .sidebar .search-box { background: rgba(255, 255, 255, 0.1) !important; border-color: rgba(255, 255, 255, 0.2) !important; }
        .sidebar .search-box svg { color: rgba(255, 255, 255, 0.7) !important; }
        .sidebar .search-box input { color: #ffffff !important; }
        .sidebar .search-box input::placeholder { color: rgba(255, 255, 255, 0.5) !important; }
        .sidebar .search-box .shortcut { background: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; }
        .sidebar .sidebar-footer { border-top: 1px solid rgba(255, 255, 255, 0.1) !important; }

        /* Collapsed Sidebar State */
        .sidebar { transition: width 0.3s ease; }
        .main-content { transition: margin-left 0.3s ease; }
        .sidebar.collapsed { width: 80px; }
        .main-content.collapsed { margin-left: 80px; }
        .sidebar.collapsed .logo-wrap { display: none; }
        .sidebar.collapsed .sidebar-header { justify-content: center; padding: 20px 0 10px; }
        .sidebar.collapsed .sidebar-search { display: none; }
        .sidebar.collapsed .nav-group-title { display: none; }
        .sidebar.collapsed .nav-item { font-size: 0; justify-content: center; padding: 12px; }
        .sidebar.collapsed .nav-item svg { margin: 0; }
        .sidebar.collapsed .sidebar-footer { flex-direction: column; padding: 15px 0; gap: 15px; }
        .sidebar.collapsed .user-info { display: none; }
        .sidebar.collapsed .user-profile-sidebar { justify-content: center; }
        
        /* LoRa Signal */
        .lora-signal {
            display: flex; align-items: flex-end; gap: 4px; height: 24px; padding-right: 15px; border-right: 1px solid var(--border-color);
        }
        .lora-bar {
            width: 4px; background: var(--border-color); border-radius: 2px; transition: 0.3s;
        }
        .lora-bar.active { background: var(--success); }
        .lora-status-text {
            font-size: 11px; font-weight: 700; color: var(--text-muted); margin-left: 5px; margin-bottom: 2px;
        }
    </style>
    @yield('styles')
</head>
<body>
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Logo & Header -->
        <div class="sidebar-header">
            <div class="logo-wrap">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <h2>TENGGANGLOPI</h2>
            </div>
            <button class="btn-collapse">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
            </button>
        </div>

        

        <!-- Menu Navigation -->
        <div class="nav-menu">
            <!-- No Group Title for top items -->
            <a href="{{ route('petugas.dashboard') }}" class="nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Dashboard
            </a>
            <a href="{{ route('petugas.tracking') ?? '#' }}" class="nav-item {{ request()->routeIs('petugas.tracking') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="10" r="3"></circle><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path></svg>
                Peta Perairan
            </a>
            <a href="{{ route('petugas.history') }}" class="nav-item {{ request()->routeIs('petugas.history') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Riwayat
            </a>
            <a href="{{ route('petugas.settings') }}" class="nav-item {{ request()->routeIs('petugas.settings') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Settings
            </a>



            <!-- Group 3 -->
            <div class="nav-group-title">Administration</div>
            
            <a href="{{ route('petugas.settings') }}" class="nav-item {{ request()->routeIs('petugas.settings') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Settings
            </a>
    </div>

    <!-- User Profile Footer -->
        <div class="sidebar-footer">
            <div class="user-profile-sidebar">
                <div class="avatar">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=262933&color=E2E8F0" alt="Avatar">
                </div>
                <div class="user-info">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="email">{{ Auth::user()->email }}</div>
                </div>
            </div>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout-icon" title="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h1>@yield('title', 'Dashboard Petugas')</h1>
                <p>@yield('subtitle', 'System Status: Healthy')</p>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="topbar-clock-card">
                    <div style="display:flex; justify-content:space-between; gap: 10px; margin-bottom:5px;">
                        <div class="date-text" id="topDateText">--</div>
                        <div class="date-text" style="color:var(--accent);">Standar Waktu Indonesia</div>
                    </div>
                    <div class="time-container">
                        <div class="local-time">
                            <span id="topTimeLocal">-- : -- : --</span>
                        </div>
                        <div class="time-divider">/</div>
                        <div class="utc-time">
                            <span id="topTimeUTC">-- : -- : --</span>
                            <small>UTC</small>
                        </div>
                    </div>
                </div>
                <div class="role-badge" style="align-self: center;">{{ Auth::user()->role ?? 'Petugas' }}</div>
            </div>
        </div>

        @yield('content')
    </main>

    @yield('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btnCollapse = document.querySelector('.btn-collapse');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            const iconHamburger = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>`;
            const iconX = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;
            
            // Set default icon based on state
            btnCollapse.innerHTML = sidebar.classList.contains('collapsed') ? iconHamburger : iconX;
    
            btnCollapse.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('collapsed');
                btnCollapse.innerHTML = sidebar.classList.contains('collapsed') ? iconHamburger : iconX;
            });
        });

        function updateTopbarClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const day = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            
            const elDate = document.getElementById('topDateText');
            if(elDate) elDate.innerText = `${dayName}, ${day} ${month} ${year}`;
            
            const witaTime = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Makassar', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            }).format(now);
            
            const utcTime = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'UTC', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            }).format(now);
            
            const elLocal = document.getElementById('topTimeLocal');
            if(elLocal) elLocal.innerText = witaTime.replace(/:/g, ' : ');
            
            const elUTC = document.getElementById('topTimeUTC');
            if(elUTC) elUTC.innerText = utcTime.replace(/:/g, ' : ');
        }
        setInterval(updateTopbarClock, 1000);
        updateTopbarClock();
    </script>
</body>
</html>
