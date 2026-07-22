<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TENGGANG LOPI Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet JS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0b1120; /* Very dark blue from screenshot */
            color: #e2e8f0;
            margin: 0;
            overflow: hidden;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        .sidebar { background-color: #1e293b; border-right: 1px solid #334155; }
        .top-header { background-color: #0f172a; border-bottom: 1px solid #334155; }
        .card-bg { background-color: #162032; border: 1px solid #1e293b; }
        
        .pulse-dot {
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .active-menu {
            background-color: #1e3a8a; /* Blue 900 */
            border: 1px solid #2563eb; /* Blue 600 */
            color: white;
        }
        
        /* Layout Grid */
        .app-layout {
            display: flex;
            height: 100vh;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .scrollable-area {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
    </style>
</head>
<body>

<div class="app-layout relative">
    <!-- Sidebar Overlay for mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar w-64 flex-shrink-0 flex flex-col h-full z-50 fixed lg:relative lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out">
        <!-- Admin Profile -->
        <div class="p-4 border-b border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-900/50 border border-blue-500 flex items-center justify-center text-blue-400 relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-800"></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-white">Komandan SAR Ma...</div>
                    <div class="text-[10px] text-blue-400">Admin Base Station</div>
                    <div class="text-[9px] text-gray-400 flex items-center gap-1 mt-0.5"><div class="w-1.5 h-1.5 bg-green-500 rounded-full pulse-dot"></div> ONLINE</div>
                </div>
            </div>
            <button class="text-gray-500 hover:text-white">&times;</button>
        </div>

        <!-- Menu -->
        <div class="p-4 flex-1 overflow-y-auto">
            <div class="text-xs font-semibold text-slate-500 mb-3 tracking-wider">MENU UTAMA</div>
            <nav class="space-y-1">
                <a id="menu-dashboard" href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg active-menu text-white text-sm font-medium transition-colors cursor-pointer" onclick="switchView('dashboard')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard Utama
                </a>
                <a id="menu-peta" href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white text-sm font-medium transition-colors cursor-pointer" onclick="switchView('peta')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    Peta Resolusi
                </a>
                <a id="menu-armada" href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white text-sm font-medium transition-colors cursor-pointer" onclick="switchView('armada')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Data Armada
                </a>
                <a id="menu-riwayat" href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white text-sm font-medium transition-colors cursor-pointer" onclick="switchView('riwayat')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Kejadian
                </a>
                <a id="menu-pengaturan" href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white text-sm font-medium transition-colors cursor-pointer" onclick="switchView('pengaturan')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Pengaturan Alat
                </a>
                <a id="menu-akun" href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white text-sm font-medium transition-colors cursor-pointer" onclick="switchView('akun')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Kelola Akun (Superadmin)
                </a>
            </nav>

            <div class="mt-8">
                <div class="text-xs font-semibold text-slate-500 mb-3 tracking-wider">FILTER PETA</div>
                <label class="flex items-center gap-3 px-3 py-2 rounded-lg border border-green-500/30 bg-green-900/10 cursor-pointer">
                    <div class="w-4 h-4 rounded-full border border-green-500 flex items-center justify-center bg-green-500/20">
                        <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="text-sm font-medium text-green-400">Kapal Aman</span>
                </label>
            </div>
        </div>

        <!-- Weather Widget -->
        <div class="p-4 border-t border-slate-700 bg-slate-800/30">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-xs font-bold text-white">PELABUHAN MAJENE</span>
            </div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xl">🌤️</span>
                <span class="text-sm font-semibold text-white">Cerah Berawan</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> 29°C</span>
                <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg> 12km/h</span>
            </div>
        </div>
    </aside>

    <!-- Main Section -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header h-16 flex items-center justify-between px-4 lg:px-6 z-10 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button class="lg:hidden text-slate-400 hover:text-white mr-2" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <h1 class="text-sm lg:text-lg font-bold text-white tracking-wide truncate">TENGGANG LOPI COMMAND CENTER<span class="font-normal text-slate-300 hidden md:inline"> - Sistem Mitigasi Laka Laut</span></h1>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-xs text-slate-300 text-right">
                    <div id="date-display" class="font-medium">--</div>
                    <div id="time-display" class="text-slate-400">--:--:--</div>
                </div>
                <div class="flex items-center gap-2 bg-slate-800/50 px-3 py-1.5 rounded-full border border-slate-700">
                    <div class="w-2.5 h-2.5 bg-green-500 rounded-full pulse-dot" id="gateway-status-dot"></div>
                    <span class="text-xs font-medium text-slate-300" id="gateway-status-text">Gateway Online</span>
                </div>
                <div class="flex items-center gap-3 border-l border-slate-700 pl-6">
                    <div class="w-8 h-8 rounded-full bg-blue-900/50 border border-blue-500 flex items-center justify-center text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">Armawan</div>
                        <div class="text-[10px] text-slate-400">admin@sar.gov.id</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </header>

        <!-- Scrollable Area -->
        <div class="scrollable-area relative">
            
            <!-- VIEW 1: Main Dashboard -->
            <div id="view-dashboard" class="space-y-6">
                <!-- Metrik Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card-bg rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-medium text-slate-400 mb-1">Total Kapal Terdaftar</div>
                            <div class="text-3xl font-bold text-white" id="val-total">0</div>
                        </div>
                        <div class="text-blue-500"><svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
                    </div>
                    <div class="card-bg rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-medium text-slate-400 mb-1">Kapal Aktif (Online)</div>
                            <div class="text-3xl font-bold text-green-400" id="val-online">0</div>
                        </div>
                        <div class="text-green-500"><svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg></div>
                    </div>
                    <div class="card-bg rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-medium text-slate-400 mb-1">Kapal Darurat / SOS</div>
                            <div class="text-3xl font-bold text-slate-500" id="val-sos">0</div>
                        </div>
                        <div class="text-slate-500"><svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
                    </div>
                </div>

                <!-- Emergency Log -->
                <div class="card-bg rounded-xl overflow-hidden border border-red-900/30">
                    <div class="bg-red-900/20 px-4 py-3 flex items-center gap-2 border-b border-red-900/30">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500 pulse-dot"></div>
                        <h2 class="text-sm font-bold text-red-400 tracking-wide uppercase">Pusat Informasi Darurat & Log Event</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-500 border-b border-slate-700 bg-slate-800/30">
                                <tr>
                                    <th class="px-4 py-3 font-medium">WAKTU</th>
                                    <th class="px-4 py-3 font-medium">PERISTIWA</th>
                                    <th class="px-4 py-3 font-medium text-right">STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="log-table-body" class="divide-y divide-slate-800 text-slate-300">
                                <!-- Logs will be inserted here -->
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-slate-500">Tidak ada log terbaru</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Peta & Daftar Kapal -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Map -->
                    <div class="lg:col-span-2 card-bg rounded-xl overflow-hidden flex flex-col h-[400px]">
                        <div class="px-4 py-3 border-b border-slate-700 flex items-center gap-2 bg-slate-800/30">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <h3 class="text-sm font-semibold text-white">Peta Lokasi Armada</h3>
                        </div>
                        <div id="map" class="flex-1 w-full relative z-0"></div>
                    </div>
                    
                    <!-- Ship List -->
                    <div class="card-bg rounded-xl flex flex-col h-[400px]">
                        <div class="px-4 py-3 border-b border-slate-700 flex items-center justify-between bg-slate-800/30">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                                <h3 class="text-sm font-semibold text-white">Daftar Kapal Nelayan</h3>
                            </div>
                            <div class="relative">
                                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input type="text" placeholder="Cari ID atau Nama Kapal..." class="bg-slate-800 border border-slate-700 text-xs rounded-md pl-8 pr-2 py-1.5 focus:outline-none focus:border-blue-500 w-40 text-white placeholder-slate-500">
                            </div>
                        </div>
                        <div class="overflow-y-auto flex-1">
                            <table class="w-full text-left text-xs">
                                <thead class="text-slate-500 border-b border-slate-700">
                                    <tr>
                                        <th class="px-3 py-2 font-medium">NODE</th>
                                        <th class="px-3 py-2 font-medium">KAPAL</th>
                                        <th class="px-3 py-2 font-medium text-center">KONDISI</th>
                                        <th class="px-3 py-2 font-medium text-right">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="ship-list-body" class="divide-y divide-slate-800">
                                    <!-- Dynamic Ship List -->
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-t border-slate-700">
                            <button class="w-full py-2 bg-blue-900/20 hover:bg-blue-900/40 border border-blue-500/30 text-blue-400 text-xs font-semibold rounded-lg transition-colors">Sinkronisasi Satelit (OpenWeather)</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW 2: Peta Resolusi -->
            <div id="view-peta" class="hidden space-y-6">
                <div class="card-bg rounded-xl p-10 flex flex-col items-center justify-center min-h-[400px] text-center text-slate-400">
                    <svg class="w-20 h-20 mb-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    <h2 class="text-2xl font-bold text-white mb-2">Peta Resolusi Tinggi</h2>
                    <p class="max-w-md">Fitur peta dengan resolusi tinggi (Satelit & Topografi Detail) sedang dalam tahap pengembangan.</p>
                </div>
            </div>

            <!-- VIEW 3: Data Armada -->
            <div id="view-armada" class="hidden space-y-6">
                <div class="card-bg rounded-xl p-10 flex flex-col items-center justify-center min-h-[400px] text-center text-slate-400">
                    <svg class="w-20 h-20 mb-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <h2 class="text-2xl font-bold text-white mb-2">Manajemen Data Armada</h2>
                    <p class="max-w-md">Halaman untuk melakukan penambahan, perubahan, dan penghapusan data kapal serta ID Node.</p>
                </div>
            </div>

            <!-- VIEW 4: Riwayat Kejadian -->
            <div id="view-riwayat" class="hidden space-y-6">
                <div class="card-bg rounded-xl p-10 flex flex-col items-center justify-center min-h-[400px] text-center text-slate-400">
                    <svg class="w-20 h-20 mb-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h2 class="text-2xl font-bold text-white mb-2">Riwayat Kejadian & Log</h2>
                    <p class="max-w-md">Arsip data insiden, sinyal SOS, peringatan cuaca, dan rekaman telemetri masa lampau.</p>
                </div>
            </div>

            <!-- VIEW 5: Pengaturan Alat -->
            <div id="view-pengaturan" class="hidden space-y-6">
                <div class="card-bg rounded-xl p-10 flex flex-col items-center justify-center min-h-[400px] text-center text-slate-400">
                    <svg class="w-20 h-20 mb-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <h2 class="text-2xl font-bold text-white mb-2">Konfigurasi Gateway & Sensor</h2>
                    <p class="max-w-md">Pengaturan sensitivitas alarm, interval pengiriman LoRa, dan integrasi API cuaca.</p>
                </div>
            </div>

            <!-- VIEW 6: Kelola Akun -->
            <div id="view-akun" class="hidden space-y-6">
                <div class="card-bg rounded-xl p-10 flex flex-col items-center justify-center min-h-[400px] text-center text-slate-400">
                    <svg class="w-20 h-20 mb-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <h2 class="text-2xl font-bold text-white mb-2">Manajemen Pengguna</h2>
                    <p class="max-w-md">Tambah admin baru, atur hak akses base station, dan lihat log aktivitas pengguna.</p>
                </div>
            </div>

            <!-- Divider -->
            <div id="divider-detail" class="my-10 border-t border-slate-700 relative">
                <div class="absolute -top-3 left-1/2 -ml-24 px-4 bg-[#0b1120] text-slate-500 text-sm font-semibold tracking-widest uppercase">
                    Detail Sensor Armada
                </div>
            </div>

            <!-- VIEW 2: Detail Node (Always visible, scroll down to see) -->
            <div id="view-detail" class="space-y-6 pb-20">
                <div class="flex items-center gap-4 mb-2">
                    <button class="text-slate-400 hover:text-white" onclick="showDashboard()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </button>
                    <div>
                        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                            <span id="detail-kapal">--</span> 
                            <span class="text-xs font-mono bg-slate-700/50 text-slate-300 px-2 py-1 rounded border border-slate-600" id="detail-node">NODE_--</span>
                        </h2>
                        <div class="text-xs text-slate-400 mt-1 font-mono tracking-widest">LAT: <span id="detail-lat">--</span> | LNG: <span id="detail-lng">--</span></div>
                    </div>
                    <div class="ml-auto">
                        <div class="px-4 py-1.5 border border-slate-600 rounded-md text-xs font-bold tracking-widest text-slate-400" id="detail-status">OFFLINE</div>
                    </div>
                </div>

                <!-- Sensor Cards -->
                <div class="flex flex-nowrap overflow-x-auto gap-4 pb-2">
                    <!-- Kemiringan -->
                    <div class="card-bg rounded-xl p-4 flex flex-col h-[320px] w-56 flex-shrink-0 relative">
                        <div class="text-[11px] font-semibold text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            KEMIRINGAN (MPU-6050)
                        </div>
                        <div class="flex justify-between text-[9px] text-slate-500 font-mono mb-2 px-2">
                            <span>PITCH</span><span>ROLL(Y)</span><span>YAW(Z)</span>
                        </div>
                        <div class="flex justify-between text-sm text-slate-300 font-mono px-2 mb-auto">
                            <span id="det-pitch">--°</span><span id="det-roll">--°</span><span>--°</span>
                        </div>
                        <div class="mt-4 mb-4">
                            <div class="px-3 py-2 bg-slate-800/30 border border-slate-700/50 text-slate-500 text-[10px] text-center rounded-md">
                                Sensor Mati (Offline)
                            </div>
                        </div>
                        <!-- Simple visual boat -->
                        <div class="absolute bottom-6 left-0 right-0 flex justify-center">
                            <div class="w-24 h-1 border-b-[1.5px] border-slate-500 relative" id="det-boat" style="transition: transform 0.3s">
                                <div class="absolute bottom-0 left-1/2 -ml-[1px] w-[2px] h-8 bg-slate-500"></div>
                                <div class="absolute bottom-1 left-1/2 w-6 h-6 border-b-[1.5px] border-r-[1.5px] border-slate-500 transform rotate-45"></div>
                            </div>
                        </div>
                        <div class="absolute bottom-6 inset-x-6">
                           <div class="w-full border-b border-dashed border-blue-900/50"></div>
                        </div>
                    </div>

                    <!-- Cuaca -->
                    <div class="card-bg rounded-xl p-4 flex flex-col h-[320px] w-56 flex-shrink-0">
                        <div class="text-[11px] font-semibold text-slate-400 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                            CUACA (BME280)
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <div class="text-[10px] text-slate-500 mb-1">SUHU</div>
                                <div class="text-sm text-slate-300 font-mono"><span id="det-temp">--</span> °C</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-500 mb-1">RH</div>
                                <div class="text-sm text-slate-300 font-mono"><span id="det-hum">--</span> %</div>
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-500 mb-1">TEKANAN UDARA</div>
                            <div class="text-sm text-slate-300 font-mono"><span id="det-pres">--</span> hPa</div>
                        </div>
                        <div class="mt-auto">
                            <div class="px-3 py-2 bg-slate-800/30 border border-slate-700/50 text-slate-500 text-[10px] text-center rounded-md" id="det-weather">
                                Sensor Mati (Offline)
                            </div>
                        </div>
                    </div>

                    <!-- Tinggi Air -->
                    <div class="card-bg rounded-xl p-4 flex flex-col h-[320px] w-56 flex-shrink-0 relative">
                        <div class="text-[11px] font-semibold text-slate-400 mb-4 flex items-center gap-2 leading-tight">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            TINGGI AIR<br>(ULTRASONIC)
                        </div>
                        <div class="text-sm font-mono text-slate-300 mb-4"><span id="det-water">--</span> <span class="text-xs text-slate-500">cm</span></div>
                        <div class="mb-auto">
                            <div class="px-3 py-2 bg-slate-800/30 border border-slate-700/50 text-slate-500 text-[10px] text-center rounded-md">
                                Sensor Mati (Offline)
                            </div>
                        </div>
                        
                        <!-- Visual representation of water -->
                        <div class="absolute bottom-6 left-1/2 -ml-[3.5rem] w-28 h-14 rounded-b-full bg-[#0b1120] border-[1.5px] border-slate-600 overflow-hidden shadow-[inset_0_4px_10px_rgba(0,0,0,0.5)]">
                            <div id="water-fill" class="absolute bottom-0 left-0 right-0 bg-blue-500 transition-all" style="height: 0%;"></div>
                            <div class="absolute inset-0 flex items-center justify-center z-10"><span class="text-xs font-bold text-white shadow-sm">0%</span></div>
                        </div>
                        <div class="absolute bottom-[3.7rem] inset-x-6 border-t border-slate-700"></div>
                    </div>

                    <!-- Baterai -->
                    <div class="card-bg rounded-xl p-4 flex flex-col h-[320px] w-56 flex-shrink-0 relative">
                        <div class="text-[11px] font-semibold text-slate-400 mb-4 flex items-center gap-2 leading-tight">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 11a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2z"></path></svg>
                            SISA BATERAI<br>(LI-ION)
                        </div>
                        <div class="mt-4 mb-8">
                            <div class="text-3xl font-mono text-slate-300">- - %</div>
                        </div>
                        <div class="absolute bottom-16 left-4 right-4 h-1 bg-slate-700/50 rounded-full"></div>
                        <div class="mt-auto px-3 py-2 bg-slate-800/30 border border-slate-700/50 text-slate-500 text-[10px] text-center rounded-md">
                            Sensor Mati (Offline)
                        </div>
                    </div>

                    <!-- LoRa -->
                    <div class="card-bg rounded-xl p-4 flex flex-col h-[320px] w-56 flex-shrink-0">
                        <div class="text-[11px] font-semibold text-slate-400 mb-4 flex items-center gap-2 leading-tight">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                            LORA SX1276<br>(RSSI)
                        </div>
                        <div class="mt-4 mb-auto">
                            <div class="text-sm font-mono text-slate-300">- - <span class="text-[10px] text-slate-500">dBm</span></div>
                        </div>
                        <div class="mt-auto px-3 py-2 bg-slate-800/30 border border-slate-700/50 text-slate-500 text-[10px] text-center rounded-md">
                            Tidak ada sinyal
                        </div>
                    </div>

                    <!-- Big Blue Button -->
                    <div class="flex-shrink-0">
                        <button id="btn-send-ping" class="h-[320px] w-64 bg-blue-600 hover:bg-blue-500 transition-colors rounded-xl text-white font-bold text-[13px] flex flex-col items-center justify-center gap-4 shadow-[0_0_20px_rgba(37,99,235,0.2)] border border-blue-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                            <span class="px-6 text-center leading-relaxed">KIRIM SINYAL BIASA<br>(BUZZER SEKALI)</span>
                        </button>
                    </div>
                </div>

                <!-- Bottom Chart Area -->
                <div class="card-bg rounded-xl p-4 mt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <h3 class="text-sm font-semibold text-white">Suhu & Kelembapan Udara</h3>
                    </div>
                    <div class="h-40 border-l border-b border-slate-700 relative w-full flex items-end">
                        <div class="absolute bottom-0 left-0 text-[10px] text-slate-500 -ml-8 -mb-1">48931</div>
                        <div class="absolute bottom-0 right-0 text-[10px] text-slate-500 -mr-6 -mb-1">100</div>
                    </div>
                </div>    </div>
            </div>
            
        </div>
    </main>
</div>

<!-- Firebase Compat JS SDK (Untuk WebSockets ke Firestore) -->
<script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore-compat.js"></script>

<script>
    // Map setup
    const map = L.map('map', { zoomControl: false }).setView([-3.542, 118.973], 11);
    L.control.zoom({ position: 'topleft' }).addTo(map);
    
    // Topo/Satellite map feel
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    }).addTo(map);

    let marker = null;
    let currentData = null;

    // Format Date & Time
    function updateClock() {
        const now = new Date();
        const days = ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        document.getElementById('date-display').innerText = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        document.getElementById('time-display').innerText = now.toLocaleTimeString('id-ID').replace(/\./g, ':');
    }
    setInterval(updateClock, 1000);
    updateClock();

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if(sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    function switchView(viewName) {
        const views = ['dashboard', 'peta', 'armada', 'riwayat', 'pengaturan', 'akun'];
        views.forEach(v => {
            const el = document.getElementById('view-' + v);
            if(el) {
                if(v === viewName) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            }
        });
        
        // Hide detail section if it's not dashboard
        if(viewName !== 'dashboard') {
            document.getElementById('view-detail').classList.add('hidden');
            document.getElementById('divider-detail').classList.add('hidden');
        } else {
            document.getElementById('view-detail').classList.remove('hidden');
            document.getElementById('divider-detail').classList.remove('hidden');
        }

        // Update menu styling
        document.querySelectorAll('nav a').forEach(a => {
            a.classList.remove('active-menu', 'text-white');
            a.classList.add('text-slate-400');
            // Remove active SVG colors if any logic applies later
        });
        const activeMenu = document.getElementById('menu-' + viewName);
        if(activeMenu) {
            activeMenu.classList.add('active-menu', 'text-white');
            activeMenu.classList.remove('text-slate-400');
        }

        // Close sidebar on mobile
        if(window.innerWidth < 1024) {
            toggleSidebar();
        }
    }

    function showDashboard() {
        switchView('dashboard');
        document.getElementById('view-dashboard').scrollIntoView({ behavior: 'smooth' });
    }

    function showDetail(nodeId) {
        populateDetail();
        document.getElementById('view-detail').scrollIntoView({ behavior: 'smooth' });
    }

    // Inisialisasi Firebase WebSockets (Realtime)
    const firebaseConfig = {
        apiKey: "AIzaSyCAklJ55Tlufu-3_qqYhFdlCZTBRwuHcZU",
        projectId: "tenggang-lopi"
    };
    firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();
    const db = firebase.firestore();

    function initFirebase() {
        auth.signInWithEmailAndPassword("armawanome47@gmail.com", "Armawan0204")
            .then(() => {
                console.log("Firebase Auth Success");
                document.getElementById('gateway-status-dot').classList.remove('bg-red-500');
                document.getElementById('gateway-status-dot').classList.add('bg-green-500');
                document.getElementById('gateway-status-text').innerText = "Firebase Terhubung";
                
                listenToFirestore();
            })
            .catch((error) => {
                console.error("Firebase Auth Error", error);
                document.getElementById('gateway-status-dot').classList.add('bg-red-500');
                document.getElementById('gateway-status-dot').classList.remove('bg-green-500');
                document.getElementById('gateway-status-text').innerText = "Firebase Error: " + error.message;
            });
    }

    let lastFirebaseUpdate = 0;

    function listenToFirestore() {
        db.collection("fleet").doc("NODE_01").onSnapshot((doc) => {
            if (doc.exists) {
                lastFirebaseUpdate = Date.now();
                const fireData = doc.data();
                
                // Format ulang data dari Firestore ke format yang dikenali UI Dashboard
                const mappedData = {
                    node: { id: 1, name: "KAPAL NELAYAN (NODE_01)" },
                    telemetry: {
                        latitude: -3.542, // Default atau ambil dari DB jika ada
                        longitude: 118.973,
                        temperature: 29, // Data statis (jika ESP32 tidak mengirim suhu)
                        humidity: 80,
                        pressure: 1012,
                        pitch: fireData.gyroscope ? fireData.gyroscope.x : null,
                        roll: fireData.gyroscope ? fireData.gyroscope.y : null,
                        water_level: fireData.waterLevel || 0,
                        weather_condition: "Data dari Firebase",
                        created_at: new Date().toISOString()
                    },
                    alerts: [] // Kosongkan atau tambahkan logika alert jika buzzerSignal menyala
                };
                
                currentData = mappedData;
                updateWidgets(mappedData);
            } else {
                console.log("No such document!");
            }
        }, (error) => {
            console.error("Error mendengarkan Firebase:", error);
        });
    }

    // Aksi Klik Tombol Kirim Sinyal (Buzzer PING)
    document.getElementById('btn-send-ping').addEventListener('click', () => {
        sendPingToNode("NODE_01");
    });

    window.sendPingToNode = function(nodeId) {
        db.collection("fleet").doc(nodeId).update({
            buzzerSignal: "PING"
        }).then(() => {
            alert("Sinyal PING berhasil dikirim ke " + nodeId + " via Firebase!");
        }).catch((err) => {
            console.error(err);
            alert("Gagal mengirim sinyal!");
        });
    };

    function updateWidgets(data) {
        // Counts
        document.getElementById('val-total').innerText = data.node ? '1' : '0';
        const isOnline = data.telemetry && (new Date() - new Date(data.telemetry.created_at)) < 30000;
        document.getElementById('val-online').innerText = isOnline ? '1' : '0';
        
        const isSos = data.alerts && data.alerts.length > 0;
        document.getElementById('val-sos').innerText = isSos ? '1' : '0';
        if(isSos) {
            document.getElementById('val-sos').classList.add('text-red-500');
            document.getElementById('val-sos').classList.remove('text-slate-500');
        } else {
            document.getElementById('val-sos').classList.remove('text-red-500');
            document.getElementById('val-sos').classList.add('text-slate-500');
        }

        // Logs
        const logBody = document.getElementById('log-table-body');
        if(data.alerts && data.alerts.length > 0) {
            let html = '';
            data.alerts.forEach(a => {
                const date = new Date(a.created_at);
                const time = date.getHours().toString().padStart(2,'0')+'.'+date.getMinutes().toString().padStart(2,'0')+'.'+date.getSeconds().toString().padStart(2,'0');
                html += `
                <tr>
                    <td class="px-4 py-2">${time}</td>
                    <td class="px-4 py-2 text-red-300">[${a.type}] ${a.message}</td>
                    <td class="px-4 py-2 text-right"><span class="px-2 py-0.5 rounded text-[10px] font-bold border border-red-500/30 bg-red-500/10 text-red-400">WARNING</span></td>
                </tr>`;
            });
            logBody.innerHTML = html;
        } else {
            logBody.innerHTML = `
                <tr>
                    <td class="px-4 py-2">--.--.--</td>
                    <td class="px-4 py-2">[ONLINE] Node ${data.node?.name || 'NODE_01'} beroperasi normal.</td>
                    <td class="px-4 py-2 text-right"><span class="px-2 py-0.5 rounded text-[10px] font-bold border border-blue-500/30 bg-blue-500/10 text-blue-400">INFO</span></td>
                </tr>`;
        }

        // Ship List
        const shipList = document.getElementById('ship-list-body');
        if(data.node) {
            const statusClass = isOnline ? 'text-green-400' : 'text-slate-500';
            const statusText = isOnline ? 'Online' : 'Offline';
            const weatherBadge = data.telemetry?.weather_condition || 'Tidak ada data';
            
            shipList.innerHTML = `
            <tr>
                <td class="px-3 py-3 font-mono text-slate-400">NODE_01</td>
                <td class="px-3 py-3">
                    <div class="font-semibold text-slate-200">${data.node.name}</div>
                    <div class="text-[10px] text-slate-500">AI: ${weatherBadge}</div>
                </td>
                <td id="status-node-01" class="px-3 py-3 text-center ${statusClass}">${statusText}</td>
                <td class="px-3 py-3 text-right flex items-center justify-end gap-2">
                    <button class="px-3 py-1.5 border border-blue-600 rounded bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white transition-colors text-xs font-bold" onclick="sendPingToNode('NODE_01')">🔔 PING</button>
                    <button class="px-3 py-1.5 border border-slate-600 rounded bg-slate-800/50 hover:bg-slate-700 text-slate-300 transition-colors text-xs" onclick="showDetail(${data.node.id})">Pantau Detail</button>
                </td>
            </tr>`;
        } else {
            shipList.innerHTML = `<tr><td colspan="4" class="px-3 py-6 text-center text-slate-500">Tidak ada data kapal terdaftar</td></tr>`;
        }

        // Map
        if(data.telemetry && data.telemetry.latitude) {
            const latlng = [data.telemetry.latitude, data.telemetry.longitude];
            if(!marker) {
                // simple custom marker
                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class='w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-[0_0_10px_rgba(59,130,246,0.8)]'></div>`,
                    iconSize: [16, 16]
                });
                marker = L.marker(latlng, {icon: icon}).addTo(map);
                map.setView(latlng, 13);
            } else {
                marker.setLatLng(latlng);
            }
        }
        
        // Update Detail View if visible
        if(!document.getElementById('view-detail').classList.contains('hidden')) {
            populateDetail();
        }
    }

    function populateDetail() {
        if(!currentData || !currentData.node) return;
        const d = currentData;
        const t = d.telemetry || {};
        const isOnline = (new Date() - new Date(t.created_at)) < 30000;
        
        document.getElementById('detail-kapal').innerText = d.node.name;
        document.getElementById('detail-lat').innerText = t.latitude || '--';
        document.getElementById('detail-lng').innerText = t.longitude || '--';
        
        const statusEl = document.getElementById('detail-status');
        if(isOnline) {
            statusEl.innerText = "ONLINE";
            statusEl.className = "px-4 py-1.5 border border-green-600 rounded-md text-xs font-bold tracking-widest text-green-400 bg-green-900/20 shadow-[0_0_10px_rgba(34,197,94,0.2)]";
        } else {
            statusEl.innerText = "OFFLINE";
            statusEl.className = "px-4 py-1.5 border border-slate-600 rounded-md text-xs font-bold tracking-widest text-slate-400 bg-transparent shadow-none";
        }

        document.getElementById('det-temp').innerText = t.temperature || '--';
        document.getElementById('det-hum').innerText = t.humidity || '--';
        document.getElementById('det-pres').innerText = t.pressure || '--';
        document.getElementById('det-pitch').innerText = t.pitch != null ? t.pitch+'°' : '--°';
        document.getElementById('det-roll').innerText = t.roll != null ? t.roll+'°' : '--°';
        document.getElementById('det-water').innerText = t.water_level || '--';
        
        if(t.pitch != null && t.roll != null) {
            document.getElementById('det-boat').style.transform = `rotate(${t.roll}deg)`;
        }
        
        if(t.water_level != null) {
            const pct = Math.min(100, Math.max(0, (t.water_level / 100) * 100)); // assume max 100cm
            document.getElementById('water-fill').style.height = `${pct}%`;
            if(pct > 50) document.getElementById('water-fill').classList.replace('bg-blue-500', 'bg-red-500');
            else document.getElementById('water-fill').classList.replace('bg-red-500', 'bg-blue-500');
        }

        if(t.weather_condition) {
            document.getElementById('det-weather').innerText = "AI: " + t.weather_condition.toUpperCase();
            if(t.weather_condition.toLowerCase().includes('storm')) {
                document.getElementById('det-weather').className = "mt-auto px-3 py-2 border text-[10px] text-center rounded-md bg-red-900/40 border-red-500 text-red-400";
            } else {
                document.getElementById('det-weather').className = "mt-auto px-3 py-2 border text-[10px] text-center rounded-md bg-slate-800/50 border-slate-700 text-slate-300";
            }
        }
    }

    // Background Timer untuk mengecek Offline Status secara Real-Time
    setInterval(() => {
        if(!currentData || !currentData.telemetry || lastFirebaseUpdate === 0) return;
        
        // ESP32 mengirim data setiap 5 detik. 
        // Jika 6.5 detik tidak ada data masuk, langsung cap OFFLINE! (Sangat Real-Time)
        const isOnline = (Date.now() - lastFirebaseUpdate) < 6500;
        
        const statusText = isOnline ? 'Online' : 'Offline';
        const statusClass = isOnline ? 'text-green-400' : 'text-slate-500';
        
        // 1. Update Angka Online di Atas
        const valOnline = document.getElementById('val-online');
        if(valOnline) valOnline.innerText = isOnline ? '1' : '0';
        
        // 2. Update Status di Tabel Daftar Kapal
        const tdStatus = document.getElementById('status-node-01');
        if (tdStatus) {
            tdStatus.className = `px-3 py-3 text-center ${statusClass}`;
            tdStatus.innerText = statusText;
        }

        // 3. Update Status di Panel Pantau Detail
        const detailStatus = document.getElementById('detail-status');
        if (detailStatus) {
            detailStatus.innerText = statusText.toUpperCase();
            if (isOnline) {
                detailStatus.className = "px-4 py-1.5 border border-green-500/30 bg-green-500/10 rounded-md text-xs font-bold tracking-widest text-green-400";
            } else {
                detailStatus.className = "px-4 py-1.5 border border-slate-600 rounded-md text-xs font-bold tracking-widest text-slate-400";
            }
        }
    }, 3000);

    initFirebase();

</script>
</body>
</html>
