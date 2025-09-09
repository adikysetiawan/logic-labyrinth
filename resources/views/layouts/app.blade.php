<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-slate-950 bg-[radial-gradient(ellipse_at_top,rgba(30,64,175,0.12),transparent_60%),radial-gradient(ellipse_at_bottom_right,rgba(14,165,233,0.1),transparent_55%)]">
            <style>
                #sidebar{ transition: width .3s ease, transform .3s ease; }
                #sidebar.sidebar-collapsed{ width: 4.5rem; }
                @media (max-width: 767px){
                    #sidebar.mobile-hidden{ transform: translateX(-100%); }
                }
                #logoutButton .label{ display:inline; }
                #sidebar.sidebar-collapsed #logoutButton{ justify-content:center; }
                #sidebar.sidebar-collapsed #logoutButton .label{ display:none; }
                #sidebar.sidebar-collapsed #logoutButton .icon{ margin-right:0; }
                #sidebar.sidebar-collapsed .brand-text{ display:none; }
            </style>

            @php($onDashboard = request()->is('dashboard'))
            @php($onPlayers = request()->is('admin/players*'))
            @php($onLevels = request()->is('admin/levels*'))
            @php($onRekap = request()->is('admin/game-plays*'))

            <!-- Mobile top bar -->
            <div class="md:hidden flex items-center justify-between px-4 py-3 border-b border-slate-800/80 bg-slate-900/60 sticky top-0 z-30 backdrop-blur">
                <button id="openSidebar" class="btn-ll px-3 py-1.5 rounded-lg bg-white/10 text-slate-200 hover:bg-white/15" aria-label="Open menu">
                    <!-- hamburger -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path stroke-width="1.8" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="text-sm text-slate-300">Menu</span>
            </div>

            <!-- Sidebar -->
            <aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-slate-900/80 border-r border-slate-800/80 backdrop-blur-md md:flex flex-col z-40 mobile-hidden -translate-x-full md:translate-x-0">
                <div class="p-4 border-b border-slate-800/80 relative">
                    <!-- Toggle + Brand at top-left -->
                    <div class="flex items-center gap-3">
                        <button id="collapseSidebar" class="hidden md:inline-flex items-center justify-center size-9 rounded-lg bg-white/10 text-slate-200 hover:bg-white/15" title="Toggle sidebar" aria-label="Toggle sidebar">
                            <!-- hamburger icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path stroke-width="1.8" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <span class="brand-text text-sm font-extrabold tracking-wide text-indigo-200">Logic <span class="text-indigo-400">Labyrinth</span></span>
                        <img src="{{ asset('images/logos/logic labyrinth_logo.png') }}" alt="Logic Labyrinth" class="brand-text h-5 w-5 object-contain opacity-90"/>
                    </div>
                    <!-- Admin box -->
                    <div class="flex items-center gap-2 mt-6">
                        <div class="size-9 rounded-lg bg-white/5 ring-1 ring-white/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5 text-indigo-300"><path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M7 10V7a5 5 0 1110 0v3M6 10h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                        </div>
                        <div class="sidebar-labels">
                            <p class="text-sm font-extrabold text-indigo-200">Admin</p>
                            
                        </div>
                    </div>
                </div>
                <nav class="flex-1 p-3 space-y-1">
                    <a href="/dashboard" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition {{ $onDashboard ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-500/40' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        <span class="w-5 h-5 inline-block">🏠</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <a href="/admin/players" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition {{ $onPlayers ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-500/40' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        <span class="w-5 h-5 inline-block">👥</span>
                        <span class="nav-label">Data Pemain</span>
                    </a>
                    <a href="/admin/levels" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition {{ $onLevels ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-500/40' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        <span class="w-5 h-5 inline-block">🧩</span>
                        <span class="nav-label">Data Level</span>
                    </a>
                    <a href="/admin/game-plays" class="group flex items-center gap-3 px-3 py-2 rounded-lg transition {{ $onRekap ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-500/40' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        <span class="w-5 h-5 inline-block">📊</span>
                        <span class="nav-label">Rekap Permainan</span>
                    </a>
                </nav>
                <div class="p-3 border-t border-slate-800/80">
                    <a href="/logout" id="logoutButton" class="w-full btn-ll px-3 py-2 rounded-lg bg-rose-600/90 text-white font-semibold hover:bg-rose-700 transition inline-flex items-center justify-center gap-2">
                        <!-- exit icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                        <span class="label">Logout</span>
                    </a>
                </div>
            </aside>

            <!-- Page Content -->
            <main class="md:ml-64 p-6 transition-all duration-300" id="mainContent">
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const sidebar = document.getElementById('sidebar');
                const openBtn = document.getElementById('openSidebar');
                const collapseBtn = document.getElementById('collapseSidebar');
                const main = document.getElementById('mainContent');
                if (openBtn && sidebar){
                    openBtn.addEventListener('click', ()=>{
                        const hidden = sidebar.classList.contains('mobile-hidden');
                        sidebar.classList.toggle('mobile-hidden', !hidden);
                        if(hidden){ sidebar.classList.remove('-translate-x-full'); } else { sidebar.classList.add('-translate-x-full'); }
                    });
                }
                if(collapseBtn && sidebar){
                    collapseBtn.addEventListener('click', ()=>{
                        const collapsed = sidebar.classList.toggle('sidebar-collapsed');
                        document.querySelectorAll('.nav-label, .sidebar-labels, .brand-text').forEach(el=>{
                            el.classList.toggle('hidden', collapsed);
                        });
                        if(window.innerWidth >= 768){
                            main.style.marginLeft = collapsed ? '4.5rem' : '16rem';
                        }
                    });
                }
            });
        </script>
    </body>
</html>
