<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VistáBarangay | Secure Online Document Requests</title>
    <meta name="description" content="Official Barangay e-Portal for fast, secure online document requests. Submit requests from home, view processing times, and track status seamlessly.">
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
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    {{-- Premium Header Navbar --}}
    <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-3.5 group">
                <img src="{{ asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-11 h-11 flex-shrink-0 rounded-full object-cover shadow-md ring-2 ring-brand-100 group-hover:scale-105 transition-transform duration-300">
                <div>
                    <span class="font-extrabold text-lg text-slate-900 tracking-tight block leading-none">Vistá<span class="text-brand-600">Barangay</span></span>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mt-1">Official Document System</span>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                <a href="#documents" class="hover:text-brand-600 transition-colors">Available Documents</a>
                <a href="#how-it-works" class="hover:text-brand-600 transition-colors">How It Works</a>
                <a href="#faq" class="hover:text-brand-600 transition-colors">Help & FAQ</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('public.track') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all duration-200">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Track Status
                </a>
                <a href="{{ route('public.request') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Request Document
                </a>
            </div>
        </div>
    </header>

    {{-- Stunning Hero Section --}}
    <section class="hero-gradient text-white relative overflow-hidden pt-12 pb-24 md:py-32">
        {{-- Subtle Grid Overlay --}}
        <div class="absolute inset-0 opacity-[0.03] bg-[linear-gradient(to_right,#fff_1px,transparent_1px),linear-gradient(to_bottom,#fff_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
        
        {{-- Decorative Blur Circles --}}
        <div class="absolute top-1/4 left-10 w-72 h-72 bg-brand-500/20 rounded-full blur-3xl animate-pulse-subtle"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-brand-400/10 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                {{-- Left Text Area --}}
                <div class="lg:col-span-7 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-brand-200 text-xs font-semibold mb-6">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Fast, Automated & Secure Processing
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white leading-[1.1] mb-6">
                        Seamless Access to Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-300 via-brand-200 to-white">Barangay Documents</span>
                    </h1>
                    
                    <p class="text-slate-300 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 mb-10 font-normal leading-relaxed">
                        Skip the queues and request your certificates, clearances, and permits directly from your device. Real-time status tracking keeps you updated every step of the way.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('public.request') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-slate-900 bg-white hover:bg-slate-100 shadow-xl shadow-black/10 hover:shadow-brand-500/20 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2.5 text-base">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            Start New Request
                        </a>
                        <a href="{{ route('public.track') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl font-semibold text-white bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/15 transition-all duration-300 flex items-center justify-center gap-2.5 text-base">
                            <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Track Request
                        </a>
                    </div>

                    {{-- Live Quick Stats --}}
                    <div class="grid grid-cols-3 gap-6 max-w-md mx-auto lg:mx-0 mt-12 pt-8 border-t border-white/10">
                        <div>
                            <span class="block text-2xl font-bold text-white">100%</span>
                            <span class="block text-xs font-medium text-slate-400 mt-0.5">Online verified</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-brand-300">Secure</span>
                            <span class="block text-xs font-medium text-slate-400 mt-0.5">CSPRNG codes</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-white">24/7</span>
                            <span class="block text-xs font-medium text-slate-400 mt-0.5">Tracking access</span>
                        </div>
                    </div>
                </div>

                {{-- Right Hero Floating Graphic / Mockup --}}
                <div class="lg:col-span-5 relative hidden sm:block">
                    <div class="relative w-full max-w-md mx-auto animate-float">
                        {{-- Decorative Glass Layer --}}
                        <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-white/5 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl transform rotate-3 scale-105"></div>
                        
                        {{-- Mockup Main Card --}}
                        <div class="relative bg-white rounded-2xl shadow-2xl p-6 text-slate-800 border border-white/40">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-xs">📜</div>
                                    <div>
                                        <span class="font-bold text-xs text-slate-900 block">Live Verification</span>
                                        <span class="text-[10px] text-slate-400 block">System Monitor</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">SECURE</span>
                            </div>

                            <div class="space-y-4 pt-4">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-xs font-bold text-slate-700">Barangay Clearance</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600">Pending Review</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono">
                                        <span>Code: BDRS-EACD7E</span>
                                        <span>Today</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 opacity-80">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-xs font-bold text-slate-700">Certificate of Indigency</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">Released</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono">
                                        <span>Code: BDRS-90A81C</span>
                                        <span>May 12</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 opacity-60">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-xs font-bold text-slate-700">Business Permit</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">Processing</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[11px] text-slate-400 font-mono">
                                        <span>Code: BDRS-71FB42</span>
                                        <span>May 11</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                                <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> 256-bit encrypted</span>
                                <span>Powered by VistáBarangay</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Beautiful Wave Divider bottom --}}
        <div class="absolute bottom-0 inset-x-0 h-12 sm:h-20 overflow-hidden pointer-events-none">
            <svg class="absolute bottom-0 w-full h-full text-[#f8fafc]" viewBox="0 0 1440 120" preserveAspectRatio="none" fill="currentColor">
                <path d="M0,32L60,42.7C120,53,240,75,360,74.7C480,75,600,53,720,48C840,43,960,53,1080,58.7C1200,64,1320,64,1380,64L1440,64L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,1200,120,60,120L0,120Z"></path>
            </svg>
        </div>
    </section>

    {{-- Available Documents Grid Section --}}
    <section id="documents" class="py-20 max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-brand-600 font-bold text-xs tracking-wider uppercase bg-brand-50 px-3 py-1 rounded-full border border-brand-100">Official Catalog</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mt-3 mb-4">Available Barangay Documents</h2>
            <p class="text-slate-500 text-base">Select the document you require to view details, mandatory processing times, and associated administrative fees.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($documentTypes as $type)
            <div class="premium-card p-7 rounded-2xl flex flex-col justify-between group">
                <div>
                    {{-- Header Row --}}
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-50 to-slate-50 border border-brand-100/50 flex items-center justify-center text-brand-600 group-hover:scale-110 transition-transform duration-300 shadow-sm flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="inline-flex items-center gap-1 font-semibold text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                            🕒 {{ $type->processing_days }} {{ Str::plural('day', $type->processing_days) }}
                        </span>
                    </div>

                    <h3 class="font-bold text-lg text-slate-900 mb-2 group-hover:text-brand-600 transition-colors">{{ $type->name }}</h3>
                    <p class="text-slate-500 text-sm font-normal line-clamp-3 leading-relaxed mb-6">{{ $type->description ?? 'Official authenticated certificate issued under the jurisdiction of the Barangay administration.' }}</p>
                </div>

                <div class="pt-5 border-t border-slate-100/80 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 block uppercase tracking-wider">Processing Fee</span>
                        <span class="font-extrabold text-lg text-slate-900 block">₱{{ number_format($type->fee, 2) }}</span>
                    </div>
                    <a href="{{ route('public.request', ['doc_type' => $type->id]) }}" class="inline-flex items-center gap-1.5 font-bold text-xs text-brand-600 group-hover:translate-x-1 transition-transform duration-200">
                        Request Now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="how-it-works" class="bg-white py-20 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-brand-600 font-bold text-xs tracking-wider uppercase bg-brand-50 px-3 py-1 rounded-full border border-brand-100">Simplified Workflow</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mt-3 mb-4">Simple 3-Step Process</h2>
                <p class="text-slate-500 text-base">We optimized the request lifecycle so you can obtain your critical documentation efficiently without unnecessary delays.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative">
                {{-- Decorative Link line for desktop --}}
                <div class="absolute top-1/2 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-brand-100 via-brand-200 to-emerald-100 hidden md:block -translate-y-6 z-0"></div>

                @foreach([
                    ['step' => '01', 'title' => 'Submit Online Form', 'desc' => 'Complete the streamlined request application securely and attach mandatory supporting reasoning.', 'icon' => '📝', 'color' => 'text-brand-600', 'bg' => 'bg-brand-50', 'border' => 'border-brand-200'],
                    ['step' => '02', 'title' => 'Real-Time Verification', 'desc' => 'Receive your highly secure tracking code immediately. Staff reviews and authenticates records dynamically.', 'icon' => '🛡️', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200'],
                    ['step' => '03', 'title' => 'Claim Your Document', 'desc' => 'Once status reflects "Released", visit the main administration hall with any identity card to pick up.', 'icon' => '✅', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200'],
                ] as $item)
                <div class="relative z-10 bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-md transition-shadow text-center flex flex-col items-center">
                    <div class="w-16 h-16 rounded-2xl {{ $item['bg'] }} {{ $item['border'] }} border flex items-center justify-center text-2xl mb-5 shadow-inner">
                        {{ $item['icon'] }}
                    </div>
                    
                    <span class="text-xs font-black {{ $item['color'] }} uppercase tracking-widest block mb-1.5">{{ $item['step'] }}</span>
                    <h3 class="font-bold text-lg text-slate-900 mb-2.5">{{ $item['title'] }}</h3>
                    <p class="text-slate-500 text-sm font-normal leading-relaxed">{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Interactive FAQ Section --}}
    <section id="faq" class="py-20 max-w-4xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-brand-600 font-bold text-xs tracking-wider uppercase bg-brand-50 px-3 py-1 rounded-full border border-brand-100">Help Desk</span>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mt-3 mb-2">Frequently Asked Questions</h2>
            <p class="text-slate-500 text-sm">Have questions about requesting documents online? Find quick answers below.</p>
        </div>

        <div class="space-y-4" x-data="{ activeTab: null }">
            @foreach([
                ['q' => 'How secure is my personal identity data?', 'a' => 'Extremely secure. All network communication utilizes industry-standard encryption, and requests are tagged with secure, high-entropy random identifiers to prevent unwanted third-party data tracking.'],
                ['q' => 'Can someone else claim my document on my behalf?', 'a' => 'Yes, authorized representatives can claim your released documentation provided they present a signed authorization letter from you alongside their valid primary identity cards.'],
                ['q' => 'What happens if my request is flagged as rejected?', 'a' => 'If a document request cannot be approved due to discrepancies or pending liabilities, our administrators log specific actionable reasoning. You can view the precise reason instantly using your tracking code.'],
                ['q' => 'Are there transaction limits on request volumes?', 'a' => 'Yes, to enforce security and prevent platform abuse, residents are limited to requesting a maximum of 2 separate documents per day, and concurrent pending requests for the same document type are automatically prohibited.'],
            ] as $idx => $faq)
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden transition-all duration-200">
                <button @click="activeTab === {{ $idx }} ? activeTab = null : activeTab = {{ $idx }}" class="w-full px-6 py-4 text-left font-bold text-slate-800 flex items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                    <span class="text-sm sm:text-base">{{ $faq['q'] }}</span>
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180 text-brand-600': activeTab === {{ $idx }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="activeTab === {{ $idx }}" x-collapse x-transition class="px-6 pb-4 pt-1 text-sm text-slate-600 border-t border-slate-100 leading-relaxed font-normal">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Help Support Card --}}
        <div class="mt-12 bg-gradient-to-r from-brand-900 to-slate-900 rounded-2xl p-8 text-white flex flex-col sm:flex-row items-center justify-between gap-6 relative overflow-hidden shadow-xl">
            <div class="absolute right-0 top-0 w-64 h-64 bg-brand-500/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 text-center sm:text-left">
                <h3 class="font-bold text-lg text-white mb-1">Need manual verification assistance?</h3>
                <p class="text-slate-400 text-sm max-w-md">Our barangay administration staff is available during standard business hours to assist you directly.</p>
            </div>
            <a href="tel:1234567" class="relative z-10 px-5 py-3 rounded-xl font-bold text-xs bg-brand-500 hover:bg-brand-400 text-white transition-colors whitespace-nowrap shadow-lg shadow-brand-500/30">
                📞 Call Front Desk
            </a>
        </div>
    </section>

    {{-- Premium Footer --}}
    <footer class="bg-slate-900 text-slate-400 pt-16 pb-12 border-t border-slate-800/80">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-slate-800">
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-9 h-9 flex-shrink-0 rounded-full object-cover shadow-sm ring-1 ring-slate-700">
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
                    <li><a href="#documents" class="hover:text-white transition-colors">Catalog & Processing Timelines</a></li>
                </ul>
            </div>

            <div class="md:col-span-3 space-y-3">
                <span class="text-xs font-bold text-white uppercase tracking-wider block">Security & Auditing</span>
                <p class="text-[11px] text-slate-500 leading-normal">
                    Authorized actions enforce role validation layers and secure event tracking to audit all platform behaviors.
                </p>
                <div class="pt-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition-colors">
                        🔒 Admin Gateway
                    </a>
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
