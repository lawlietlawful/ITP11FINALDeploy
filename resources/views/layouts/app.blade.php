<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VistáBarangay - Document Request System</title>
    <meta name="description" content="VistáBarangay - Manage residents and process official barangay documents">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cd0fd',
                            400: '#36bffa',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cd0fd',
                            400: '#36bffa',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                        accent: { 50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d' },
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #e0effe 40%, #f8fafc 100%);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        }

        .sidebar-link {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
        }
        .sidebar-link:hover {
            background: rgba(2, 132, 199, 0.06);
            color: #0284c7;
            transform: translateX(2px);
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.35);
        }
        .sidebar-link.active svg {
            color: white;
        }

        /* Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 18px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        /* Stat cards */
        .stat-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 16px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .overview-card {
            padding: 1rem;
        }

        .overview-card-body {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .overview-card-kicker {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 0.35rem;
        }

        .overview-card-value {
            font-size: 2rem;
            line-height: 1;
            font-weight: 800;
            color: #111827;
        }

        .overview-card-caption {
            margin-top: 0.45rem;
            font-size: 0.6875rem;
            line-height: 1.45;
            font-weight: 600;
        }

        .overview-card-icon {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
            flex-shrink: 0;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .overview-card:hover .overview-card-icon {
            transform: scale(1.06);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88), 0 10px 20px rgba(148, 163, 184, 0.12);
        }

        .overview-card-icon svg {
            width: 1.4rem;
            height: 1.4rem;
        }

        .overview-card-icon-blue {
            background: linear-gradient(135deg, rgba(219, 234, 254, 0.9) 0%, rgba(239, 246, 255, 1) 100%);
            border-color: rgba(191, 219, 254, 0.95);
            color: #2563eb;
        }

        .overview-card-icon-emerald {
            background: linear-gradient(135deg, rgba(209, 250, 229, 0.95) 0%, rgba(236, 253, 245, 1) 100%);
            border-color: rgba(167, 243, 208, 0.95);
            color: #059669;
        }

        .overview-card-icon-amber {
            background: linear-gradient(135deg, rgba(254, 243, 199, 0.95) 0%, rgba(255, 251, 235, 1) 100%);
            border-color: rgba(252, 211, 77, 0.35);
            color: #d97706;
        }

        .overview-card-icon-violet {
            background: linear-gradient(135deg, rgba(237, 233, 254, 0.95) 0%, rgba(245, 243, 255, 1) 100%);
            border-color: rgba(221, 214, 254, 0.95);
            color: #7c3aed;
        }

        .overview-card-icon-rose {
            background: linear-gradient(135deg, rgba(255, 228, 230, 0.95) 0%, rgba(255, 241, 242, 1) 100%);
            border-color: rgba(254, 205, 211, 0.95);
            color: #e11d48;
        }

        .table-shell {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table {
            min-width: 760px;
        }

        @media (max-width: 768px) {
            .overview-card {
                padding: 0.9rem;
            }

            .overview-card-value {
                font-size: 1.75rem;
            }

            .overview-card-icon {
                width: 3rem;
                height: 3rem;
                border-radius: 1rem;
            }
        }

        /* Table */
        .table-row { transition: all 0.15s ease; }
        .table-row:hover { background: rgba(240, 247, 255, 0.6); }

        /* Animations */
        .fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Main content wrapper */
        .main-content-area {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* Scrollbars */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
        
        .sidebar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); }
        [x-cloak] { display: none !important; }

        /* Input styling */
        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #7cd0fd;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            background: white;
        }

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 10px 22px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.4);
            transform: translateY(-1px);
        }

        .btn-secondary {
            padding: 10px 22px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
            background: rgba(255,255,255,0.8);
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background: rgba(248, 250, 252, 1);
            border-color: #cbd5e1;
            color: #475569;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            color: white !important;
            padding: 10px 22px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.3) !important;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none !important;
        }
        .btn-danger:hover {
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4) !important;
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            color: white !important;
            padding: 10px 22px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3) !important;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none !important;
        }
        .btn-success:hover {
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4) !important;
            transform: translateY(-1px);
        }

        /* Fix: Prevent backdrop-filter white wash on modal content */
        [x-teleport] + div,
        div[x-show] > div > div > div.inline-block,
        .fixed.inset-0 .inline-block {
            isolation: isolate;
        }
        /* Ensure modal buttons are never covered by backdrop stacking */
        .modal-actions {
            position: relative;
            z-index: 10;
            isolation: isolate;
        }
        .modal-actions button,
        .modal-actions form button {
            position: relative;
            z-index: 10;
        }
    </style>
    @stack('styles')
</head>
<body class="text-gray-800 overflow-hidden">

    <div class="flex h-screen w-full">

        {{-- Sidebar --}}
        <aside class="sidebar w-[260px] flex-shrink-0 flex flex-col px-5 pt-6 lg:pt-8 pb-5 my-5 lg:my-7 ml-5 lg:ml-7 sticky top-5 lg:top-7 h-[calc(100vh-40px)] lg:h-[calc(100vh-56px)] rounded-3xl overflow-y-auto hidden md:flex">

            {{-- Brand --}}
            <div class="mb-8 px-2">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-10 h-10 flex-shrink-0 rounded-full object-cover shadow-md ring-2 ring-primary-100">
                    <div>
                        <span class="font-bold text-[18px] text-gray-900 tracking-tight">Vistá<span class="text-primary-600">Barangay</span></span>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1">
                @php
                    $links = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'],
                        ['route' => 'residents.index', 'label' => 'Residents', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                        ['route' => 'requests.index', 'label' => 'Requests', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
                        ['route' => 'document-types.index', 'label' => 'Document Types', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>'],
                        ['route' => 'reports.index', 'label' => 'Reports', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                    ];
                @endphp

                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 text-[13px] font-medium
                              {{ request()->routeIs($link['route'].'*') ? 'active' : 'text-gray-500' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0 {{ request()->routeIs($link['route'].'*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $link['icon'] !!}</svg>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Logout --}}
            <div class="mt-auto pt-4 border-t border-gray-200/60">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-link flex items-center gap-3 px-3.5 py-2.5 text-[13px] font-medium text-gray-500 hover:text-red-600 hover:bg-red-50/80 w-full transition-all rounded-xl">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 p-5 lg:p-7 h-screen overflow-hidden fade-in flex flex-col">
            <div class="main-content-area p-6 lg:p-8 flex-1 overflow-y-auto">

                {{-- Flash Messages --}}
                <div class="pointer-events-none fixed right-5 top-5 z-50 flex w-[min(100%-2rem,22rem)] flex-col gap-2.5 sm:right-7 sm:top-7">
                    @if(session('success'))
                        <x-alert type="success" :autohide="4200">
                            {{ session('success') }}
                        </x-alert>
                    @endif

                    @if(session('warning'))
                        <x-alert type="warning" title="Please Review" :autohide="5200">
                            {{ session('warning') }}
                        </x-alert>
                    @endif

                    @if(session('error'))
                        <x-alert type="error" :autohide="5600">
                            {{ session('error') }}
                        </x-alert>
                    @endif
                </div>

                @if($errors->any())
                    <x-alert type="error" title="Please Correct The Following" class="mb-5">
                        <ul class="space-y-1.5">
                            @foreach($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="mt-[7px] h-1.5 w-1.5 flex-shrink-0 rounded-full bg-current opacity-70"></span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>
