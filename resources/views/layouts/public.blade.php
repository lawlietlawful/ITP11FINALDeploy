<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VistáBarangay | Secure Online Document Requests')</title>
    <meta name="description" content="Official Barangay e-Portal for fast, secure online document requests. Submit requests from home, view processing times, and track status seamlessly.">
    <link rel="icon" href="{{ \App\Models\Setting::getCached('system_logo') ?: asset('New Logo.png') }}" type="image/png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
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
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-subtle': 'pulseSubtle 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        pulseSubtle: {
                            '0%, 100%': { opacity: 1 },
                            '50%': { opacity: 0.8 },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .hero-gradient {
            background: radial-gradient(circle at 90% 10%, rgba(14, 165, 233, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 10% 90%, rgba(2, 132, 199, 0.1) 0%, transparent 40%),
                        linear-gradient(135deg, #082f49 0%, #0c4a6e 100%);
        }
        .premium-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .premium-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(12, 74, 110, 0.08);
            border-color: rgba(14, 165, 233, 0.3);
        }
        .input-premium {
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-premium:focus {
            background: #ffffff;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    @php
        $allowOnlineRequests = \App\Models\Setting::getCached('allow_online_requests') === 'true';
    @endphp

    {{-- Premium Header Navbar --}}
    <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-3.5 group">
                <img src="{{ \App\Models\Setting::getCached('system_logo') ?: asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-11 h-11 flex-shrink-0 rounded-full object-cover shadow-md ring-2 ring-brand-100 group-hover:scale-105 transition-transform duration-300">
                <div>
                    <span class="font-extrabold text-lg text-slate-900 tracking-tight block leading-none">Vistá<span class="text-brand-600">Barangay</span></span>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mt-1">Official Document System</span>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                <a href="{{ route('public.home') }}#documents" class="hover:text-brand-600 transition-colors">Available Documents</a>
                <a href="{{ route('public.home') }}#how-it-works" class="hover:text-brand-600 transition-colors">How It Works</a>
                <a href="{{ route('public.home') }}#faq" class="hover:text-brand-600 transition-colors">Help & FAQ</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('public.track') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all duration-200">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Track Status
                </a>
                @if($allowOnlineRequests)
                    <a href="{{ route('public.request') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Request Document
                    </a>
                @else
                    <button disabled class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-slate-400 cursor-not-allowed opacity-80">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        Requests Paused
                    </button>
                @endif
            </div>
        </div>
    </header>

    <main class="min-h-[calc(100vh-80px-300px)]">
        @yield('content')
    </main>

    {{-- Premium Footer --}}
    <footer class="bg-slate-900 text-slate-400 pt-16 pb-12 border-t border-slate-800/80 mt-auto">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-slate-800">
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ \App\Models\Setting::getCached('system_logo') ?: asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-9 h-9 flex-shrink-0 rounded-full object-cover shadow-sm ring-1 ring-slate-700">
                    <span class="font-extrabold text-base text-white tracking-tight">Vistá<span class="text-brand-400">Barangay</span></span>
                </div>
                <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                    Designed to modernize localized constituent engagement. Providing real-time audit visibility, encrypted tracking, and highly accessible document issuance interfaces.
                </p>
            </div>

            <div class="md:col-span-4 space-y-3">
                <span class="text-xs font-bold text-white uppercase tracking-wider block">Constituent Services</span>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('public.request') }}" class="hover:text-white transition-colors">Request a New Document</a></li>
                    <li><a href="{{ route('public.track') }}" class="hover:text-white transition-colors">Look Up Application Status</a></li>
                    <li><a href="{{ route('public.home') }}#documents" class="hover:text-white transition-colors">Catalog & Processing Timelines</a></li>
                </ul>
            </div>

            <div class="md:col-span-3 space-y-3">
                <span class="text-xs font-bold text-white uppercase tracking-wider block">Connect With Us</span>
                <p class="text-[11px] text-slate-500 leading-normal mb-3">
                    Stay updated with official barangay announcements.
                </p>
                <div class="flex items-center gap-3">
                    @php
                        $fb = \App\Models\Setting::where('key', 'facebook_url')->value('value');
                        $tw = \App\Models\Setting::where('key', 'twitter_url')->value('value');
                        $ig = \App\Models\Setting::where('key', 'instagram_url')->value('value');
                    @endphp
                    @if($fb)
                        <a href="{{ $fb }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-brand-500 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"></path></svg>
                        </a>
                    @endif
                    @if($tw)
                        <a href="{{ $tw }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-sky-500 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                        </a>
                    @endif
                    @if($ig)
                        <a href="{{ $ig }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-pink-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
            <p>&copy; {{ date('Y') }} Barangay Document Request System. All rights reserved.</p>
            <div class="flex gap-4">
                <span>Highly Secure Infrastructure</span>
                <span>•</span>
                <span>VistáBarangay Platform</span>
            </div>
        </div>
    </footer>

</body>
</html>
