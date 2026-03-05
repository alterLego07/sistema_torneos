<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Torneos') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    {{-- Mobile overlay --}}
    <div id="mobileOverlay" class="fixed inset-0 z-40 hidden bg-gray-900/50 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full border-r border-gray-200 bg-white lg:translate-x-0 transition-transform duration-200"
    >
        <div class="flex h-16 items-center justify-between px-4 border-b border-gray-200">
            {{-- Logo Area --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gray-900 text-white grid place-items-center font-semibold">
                    C
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-gray-900">Copa Chacomer</div>
                    <div class="text-xs text-gray-500">Panel</div>
                </div>
            </a>

            {{-- Collapse button (desktop) --}}
            <button
                id="sidebarCollapseBtn"
                type="button"
                class="hidden lg:inline-flex h-9 w-9 items-center justify-center rounded-xl hover:bg-gray-100"
                aria-label="Colapsar sidebar"
            >
                <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Close (mobile) --}}
            <button
                id="sidebarCloseBtn"
                type="button"
                class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-xl hover:bg-gray-100"
                aria-label="Cerrar sidebar"
            >
                <svg class="h-5 w-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="p-3 space-y-1">
            <div class="px-3 pt-2 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">
                General
            </div>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="sidebar-icon">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                </span>
                <span class="sidebar-text">Dashboard</span>
            </a>

            @can('tournaments.view')
                <a href="{{ route('tournaments.index') }}"
                   class="sidebar-link {{ request()->routeIs('tournaments.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Torneos</span>
                </a>
            @endcan

            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Configuración
            </div>

            @can('disciplines.manage')
                <a href="{{ route('disciplines.manager') }}"
                   class="sidebar-link {{ request()->routeIs('disciplines.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 20l9-5-9-5-9 5 9 5z"/>
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 12l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Disciplinas</span>
                </a>
            @endcan

            @can('categories.manage')
                <a href="{{ route('categories.manager') }}"
                   class="sidebar-link {{ request()->routeIs('categories.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Categorías</span>
                </a>
            @endcan

            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Registro
            </div>

            @can('players.manage')
                <a href="{{ route('players.index') }}"
                   class="sidebar-link {{ request()->routeIs('players.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4" stroke-width="2"/>
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 8v6M23 11h-6"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Jugadores</span>
                </a>
            @endcan

            @can('participants.manage')
                <a href="{{ route('participants.index') }}"
                   class="sidebar-link {{ request()->routeIs('participants.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 0 0-4-4h-1"/>
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 20H2v-2a4 4 0 0 1 4-4h1"/>
                            <circle cx="9" cy="7" r="4" stroke-width="2"/>
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 7a4 4 0 0 1 0 8"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Equipos / Parejas</span>
                </a>
            @endcan

            {{-- @can('registrations.manage')
                <a href="{{ route('registrations.index') }}"
                   class="sidebar-link {{ request()->routeIs('registrations.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M7 20h10a2 2 0 0 0 2-2V7l-4-4H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Inscripciones</span>
                </a>
            @endcan --}}

            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">
                Competición
            </div>

            @can('matches.view')
                <a href="{{ route('matches.calendar') }}"
                   class="sidebar-link {{ request()->routeIs('matches.calendar') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Calendario</span>
                </a>
            @endcan

            @can('matches.schedule')
                <a href="{{ route('matches.scheduler') }}"
                   class="sidebar-link {{ request()->routeIs('matches.scheduler') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Programación</span>
                </a>
            @endcan

            @can('results.enter')
                <a href="{{ route('matches.results') }}"
                   class="sidebar-link {{ request()->routeIs('matches.results') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5h6M9 9h6M7 21h10a2 2 0 0 0 2-2V7l-4-4H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Resultados</span>
                </a>
            @endcan

            @can('standings.view')
                <a href="{{ route('standings.table') }}"
                   class="sidebar-link {{ request()->routeIs('standings.table') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M6 17V7m6 10V5m6 12V9"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Tabla</span>
                </a>
            @endcan

            @can('brackets.view')
                <a href="{{ route('brackets.view') }}"
                   class="sidebar-link {{ request()->routeIs('brackets.view') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 7h4v4H7V7zm6 6h4v4h-4v-4z"/>
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 9h2a2 2 0 0 1 2 2v2"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Llaves</span>
                </a>
            @endcan

            @can('users.manage')
                <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    Administración
                </div>

                <a href="{{ route('admin.users') }}"
                   class="sidebar-link {{ request()->routeIs('admin.users') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4" stroke-width="2"/>
                        </svg>
                    </span>
                    <span class="sidebar-text">Usuarios</span>
                </a>
            @endcan
        </nav>

        {{-- Footer --}}
        <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200 p-3">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <div class="truncate text-sm font-medium text-gray-900">
                        {{ auth()->user()->name ?? 'Usuario' }}
                    </div>
                    <div class="truncate text-xs text-gray-500">
                        {{ auth()->user()->email ?? '' }}
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex h-9 items-center justify-center rounded-xl px-3 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div id="mainWrap" class="lg:pl-72 transition-[padding] duration-200">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/80 backdrop-blur">
            <div class="flex h-16 items-center justify-between px-4">
                <div class="flex items-center gap-2">
                    {{-- Mobile open --}}
                    <button id="sidebarOpenBtn" type="button"
                            class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-xl hover:bg-gray-100"
                            aria-label="Abrir sidebar">
                        <svg class="h-6 w-6 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="text-sm font-semibold text-gray-900">
                        {{ config('app.name', 'Sistema de Torneos') }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="hidden sm:inline text-sm text-gray-600">{{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6">
            {{ $slot }}
        </main>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const mainWrap = document.getElementById('mainWrap');
            const overlay  = document.getElementById('mobileOverlay');

            const collapseBtn = document.getElementById('sidebarCollapseBtn');
            const openBtn     = document.getElementById('sidebarOpenBtn');
            const closeBtn    = document.getElementById('sidebarCloseBtn');

            const key = 'sidebar:collapsed';
            const isCollapsed = () => localStorage.getItem(key) === '1';

            function applyCollapsedState() {
                const collapsed = isCollapsed();

                if (collapsed) {
                    sidebar.classList.add('w-20');
                    sidebar.classList.remove('w-72');

                    mainWrap.classList.add('lg:pl-20');
                    mainWrap.classList.remove('lg:pl-72');

                    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
                } else {
                    sidebar.classList.add('w-72');
                    sidebar.classList.remove('w-20');

                    mainWrap.classList.add('lg:pl-72');
                    mainWrap.classList.remove('lg:pl-20');

                    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
                }
            }

            function toggleCollapsed() {
                localStorage.setItem(key, isCollapsed() ? '0' : '1');
                applyCollapsedState();
            }

            function openMobile() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeMobile() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            applyCollapsedState();

            if (collapseBtn) collapseBtn.addEventListener('click', toggleCollapsed);
            if (openBtn) openBtn.addEventListener('click', openMobile);
            if (closeBtn) closeBtn.addEventListener('click', closeMobile);
            if (overlay) overlay.addEventListener('click', closeMobile);

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    overlay.classList.add('hidden');
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    closeMobile();
                }
            });
        })();
    </script>

    <style>
        .sidebar-link { display:flex; align-items:center; gap:.75rem; border-radius: .85rem; padding:.6rem .75rem; font-weight: 500; font-size: .95rem; }
        .sidebar-icon { display:inline-flex; height: 2.25rem; width: 2.25rem; align-items:center; justify-content:center; border-radius: .75rem; color: #4b5563; }
        .sidebar-text { white-space: nowrap; }
    </style>

    @livewireScripts
</body>
</html>