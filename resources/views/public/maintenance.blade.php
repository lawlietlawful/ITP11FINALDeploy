<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Under Maintenance | VistáBarangay</title>
    <link rel="icon" href="{{ \App\Models\Setting::getCached('system_logo') ?: asset('New Logo.png') }}" type="image/png">
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- Tailwind via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-600 min-h-screen flex flex-col items-center justify-center relative overflow-hidden">

    {{-- Background Decorative Elements --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -right-[10%] w-[70%] h-[70%] rounded-full bg-primary-100/40 blur-3xl mix-blend-multiply"></div>
        <div class="absolute -bottom-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-indigo-100/40 blur-3xl mix-blend-multiply"></div>
        <div class="absolute top-[20%] left-[20%] w-[40%] h-[40%] rounded-full bg-rose-50/40 blur-3xl mix-blend-multiply"></div>
    </div>

    {{-- Main Container --}}
    <main class="relative z-10 w-full max-w-lg px-6 py-12 mx-auto">
        <div class="bg-white/80 backdrop-blur-xl border border-white/40 shadow-2xl shadow-primary-900/5 rounded-3xl p-8 sm:p-12 text-center relative overflow-hidden">
            
            {{-- Top Accent Line --}}
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary-400 via-indigo-500 to-primary-600"></div>

            {{-- Icon/Illustration --}}
            <div class="w-24 h-24 mx-auto bg-primary-50 rounded-full flex items-center justify-center mb-8 shadow-inner ring-4 ring-white relative">
                <div class="absolute inset-0 bg-primary-400/20 rounded-full animate-ping" style="animation-duration: 3s;"></div>
                <svg class="w-10 h-10 text-primary-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>

            <h1 class="font-jakarta text-3xl font-extrabold text-slate-900 tracking-tight mb-4">
                System Offline
            </h1>
            
            <p class="text-slate-500 leading-relaxed mb-8">
                The VistáBarangay public portal is currently undergoing scheduled maintenance and upgrades to serve you better. We'll be back online shortly.
            </p>

            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 mb-8 text-left">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Need urgent assistance?</h3>
                <div class="flex items-start gap-3 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p>Please visit the Barangay Hall directly for immediate processing of your documents.</p>
                </div>
            </div>

            {{-- Optional: Back to home button (if it redirects back to maintenance, it's just a refresh, but good UX practice) --}}
            <a href="javascript:window.location.reload();" class="inline-flex items-center justify-center w-full px-6 py-3.5 text-sm font-bold text-white transition-all bg-slate-900 rounded-xl hover:bg-slate-800 hover:shadow-lg hover:-translate-y-0.5 shadow-md">
                Check Again
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </a>

        </div>

        {{-- Footer --}}
        <div class="mt-8 text-center">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} VistáBarangay. All rights reserved.</p>
        </div>
    </main>

</body>
</html>
