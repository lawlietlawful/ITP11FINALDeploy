<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Document Request Status | VistáBarangay</title>
    <meta name="description" content="Enter your secure tracking token to check real-time approval status and collection readiness for your requested document.">
    <script src="https://cdn.tailwindcss.com"></script>
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
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .input-tracking {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 0.15em;
            transition: all 0.25s ease;
        }
        .input-tracking:focus {
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
            border-color: #0ea5e9;
            outline: none;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col">

    {{-- Premium Header Navbar --}}
    <header class="sticky top-0 z-50 glass-nav">
        <div class="max-w-4xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-9 h-9 flex-shrink-0 rounded-full object-cover shadow-sm ring-2 ring-brand-100 group-hover:scale-105 transition-transform duration-200">
                <span class="font-bold text-base text-slate-900 tracking-tight">Vistá<span class="text-brand-600">Barangay</span></span>
            </a>
            
            <a href="{{ route('public.request') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 px-3.5 py-2 rounded-lg transition-colors border border-brand-100/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Request Document
            </a>
        </div>
    </header>

    {{-- Main Content Container --}}
    <main class="flex-1 max-w-3xl w-full mx-auto px-6 py-12">
        
        {{-- Header Search Title --}}
        <div class="text-center max-w-lg mx-auto mb-10">
            <div class="w-14 h-14 bg-brand-50 rounded-2xl border border-brand-100 flex items-center justify-center mx-auto mb-4 text-brand-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Track Your Request Status</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1.5">Enter the exact secure token issued during application submission to check real-time lifecycle tracking.</p>
        </div>

        {{-- Tracking Input Box Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xl shadow-slate-100 p-5 sm:p-6 mb-8">
            <form method="POST" action="{{ route('public.track.search') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <div class="relative flex-1">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs select-none">CODE:</span>
                    <input type="text" name="tracking_code" value="{{ request('tracking_code') ?? old('tracking_code') }}" required
                           class="input-tracking w-full pl-16 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-bold uppercase placeholder:text-slate-300 placeholder:font-sans placeholder:tracking-normal text-sm sm:text-base"
                           placeholder="BDRS-XXXXXXXXXX" maxlength="20" autofocus>
                </div>
                <button type="submit" class="px-8 py-3.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 shadow-lg shadow-brand-500/20 hover:shadow-brand-500/30 transition-all uppercase tracking-wider flex-shrink-0 flex items-center justify-center gap-2">
                    <span>Perform Look-up</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            @if($errors->any())
                <x-alert type="error" title="Lookup Failed" class="mt-3 mb-0 rounded-xl shadow-none backdrop-blur-0">
                    {{ $errors->first() }}
                </x-alert>
            @endif

            @isset($failedTrackingAttempts)
                @if($failedTrackingAttempts >= 3)
                    <x-alert type="warning" title="Security Notice" class="mt-3 mb-0 rounded-xl shadow-none backdrop-blur-0">
                        We noticed repeated unsuccessful lookups from this connection. For your security, temporary cooldowns will be applied if the pattern continues.
                    </x-alert>
                @endif
            @endisset
        </div>

        {{-- Status Output Card --}}
        @isset($docRequest)
            @if($docRequest)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-100 overflow-hidden">
                    {{-- Status Dynamic Header Banner --}}
                    @php
                        $statusMeta = match($docRequest->status) {
                            'pending'    => ['bg' => 'bg-amber-500', 'badge' => 'bg-amber-600', 'text' => 'Pending Staff Review', 'desc' => 'Your application has been logged safely. Administrators are querying identity proofs prior to acceptance.', 'icon' => '⏳'],
                            'processing' => ['bg' => 'bg-brand-500', 'badge' => 'bg-brand-600', 'text' => 'In Active Processing', 'desc' => 'Your document records are currently being drafted and undergoing final signature validation.', 'icon' => '⚙️'],
                            'released'   => ['bg' => 'bg-emerald-500', 'badge' => 'bg-emerald-600', 'text' => 'Ready For Claim / Released', 'desc' => 'Authentication finished successfully! Present your token code at the main administration hub to pick up.', 'icon' => '🎉'],
                            'rejected'   => ['bg' => 'bg-rose-500', 'badge' => 'bg-rose-600', 'text' => 'Application Rejected', 'desc' => 'The submission could not proceed. Review the mandatory administrative reasoning stated below.', 'icon' => '⚠️'],
                            default      => ['bg' => 'bg-slate-600', 'badge' => 'bg-slate-700', 'text' => 'Status Discrepancy', 'desc' => 'Unable to determine lifecycle mapping.', 'icon' => '❓'],
                        };
                    @endphp

                    <div class="{{ $statusMeta['bg'] }} text-white p-6 sm:p-8 relative overflow-hidden">
                        {{-- Background blur glow --}}
                        <div class="absolute right-0 top-0 w-48 h-48 bg-white/10 rounded-full blur-xl"></div>
                        
                        <div class="relative z-10 flex items-start gap-4">
                            <span class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur-md border border-white/10 flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                                {{ $statusMeta['icon'] }}
                            </span>
                            <div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 tracking-wider uppercase backdrop-blur-sm border border-white/10">Lifecycle Mapping</span>
                                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight mt-1">{{ $statusMeta['text'] }}</h2>
                                <p class="text-white/90 text-xs sm:text-sm mt-1 max-w-lg leading-relaxed font-normal">{{ $statusMeta['desc'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Visual Timeline Stages --}}
                    <div class="bg-slate-50 p-6 border-b border-slate-100">
                        <div class="max-w-md mx-auto flex items-center justify-between relative">
                            @php
                                $lifecycleSteps = ['pending', 'processing', 'released'];
                                $activeIndex = $docRequest->status === 'rejected' ? -1 : array_search($docRequest->status, $lifecycleSteps);
                            @endphp

                            @foreach($lifecycleSteps as $idx => $stepName)
                                <div class="flex flex-col items-center relative z-10">
                                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition-all
                                         {{ $idx <= $activeIndex ? 'bg-brand-600 border-brand-600 text-white shadow-sm' : 'bg-white border-slate-300 text-slate-400' }}">
                                        @if($idx < $activeIndex)
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            {{ $idx + 1 }}
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-bold mt-1.5 uppercase tracking-wider {{ $idx <= $activeIndex ? 'text-slate-800' : 'text-slate-400' }}">{{ $stepName }}</span>
                                </div>

                                @if($idx < count($lifecycleSteps) - 1)
                                    <div class="flex-1 h-0.5 mx-2 {{ $idx < $activeIndex ? 'bg-brand-600' : 'bg-slate-200' }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Data Detail Blocks --}}
                        <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Token Code</span>
                                <span class="font-mono font-bold text-sm text-slate-900 block mt-0.5 select-all">{{ $docRequest->tracking_code }}</span>
                            </div>

                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Document</span>
                            <span class="font-bold text-sm text-slate-900 block mt-0.5">{{ $docRequest->documentType->name }}</span>
                        </div>

                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Date Requested</span>
                                <span class="font-medium text-sm text-slate-800 block mt-0.5">{{ $docRequest->created_at->format('M d, Y h:i A') }}</span>
                            </div>

                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Current Status</span>
                                <span class="font-medium text-sm text-slate-800 block mt-0.5">{{ ucfirst($docRequest->status) }}</span>
                            </div>

                            @if($docRequest->released_at)
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Timestamp Issued / Released</span>
                            <span class="font-bold text-xs text-emerald-600 block mt-0.5">{{ $docRequest->released_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif

                        <div class="sm:col-span-2 border-t border-slate-100 pt-4 mt-2">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Privacy Notice</span>
                            <p class="text-xs text-slate-500 bg-slate-50 border border-slate-100 p-3 rounded-xl mt-1.5 leading-relaxed">
                                For your security, this public tracker only shows limited request progress information.
                            </p>
                        </div>
                    </div>

                    {{-- Back/Refresh bar --}}
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Data validated via CSPRNG query log</span>
                        <a href="{{ route('public.track', ['tracking_code' => $docRequest->tracking_code]) }}" class="font-bold text-brand-600 hover:text-brand-700 inline-flex items-center gap-1">
                            🔄 Reload Status
                        </a>
                    </div>
                </div>
            @else
                {{-- Not Found Card --}}
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-100 p-10 text-center max-w-md mx-auto animate-[scaleUp_0.3s_ease-out]">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-center mx-auto mb-4 text-slate-400 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 mb-1">Token Code Not Found</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        We could not match the identifier provided against our validated datastore records. Make sure typed characters exactly reflect your receipt text.
                    </p>
                    <a href="{{ route('public.track') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors inline-block">
                        Clear Search Box
                    </a>
                </div>
            @endif
        @endisset

        {{-- Help Guidance link bottom --}}
        <div class="text-center mt-12">
            <a href="{{ route('public.home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">
                ← Return to Barangay Homepage
            </a>
        </div>

    </main>

    {{-- Premium Footer Compact --}}
    <footer class="bg-slate-900 text-slate-500 py-6 border-t border-slate-800 mt-12 text-xs text-center">
        <div class="max-w-3xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} VistáBarangay. Encrypted Lookup.</p>
            <div class="flex gap-4 text-slate-600 font-medium">
                <a href="{{ route('login') }}" class="hover:text-slate-400 transition-colors">Admin Gateway</a>
            </div>
        </div>
    </footer>

</body>
</html>
