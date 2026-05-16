@extends('layouts.public')

@section('title', 'Application Received | Secure Tracking Issued')

@section('content')
    <style>
        .ticket-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(8, 47, 73, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
        }

        .ticket-card::before,
        .ticket-card::after {
            content: '';
            position: absolute;
            top: 55%;
            width: 1.5rem;
            height: 1.5rem;
            background: #f8fafc;
            border-radius: 50%;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .ticket-card::before {
            left: -0.75rem;
            border-left-color: transparent;
            border-top-color: transparent;
            transform: rotate(-45deg);
        }

        .ticket-card::after {
            right: -0.75rem;
            border-right-color: transparent;
            border-bottom-color: transparent;
            transform: rotate(-45deg);
        }

        .ticket-divider {
            border-top: 2px dashed rgba(226, 232, 240, 0.8);
            margin: 2rem 0;
        }

        .code-display {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 0.1em;
        }

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(10px, -20px) scale(1.05); }
            66% { transform: translate(-10px, 10px) scale(0.95); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>

    {{-- Main Content Container --}}
    <main class="flex-1 max-w-xl w-full mx-auto px-6 py-12 flex flex-col justify-center">
        {{-- Top Success Status Animated Badge --}}
        <div class="text-center mb-6 animate-[scaleUp_0.4s_ease-out]">
            <div
                class="w-20 h-20 bg-emerald-50 rounded-full border-8 border-emerald-500/10 flex items-center justify-center mx-auto mb-4 text-emerald-500 shadow-inner">
                <svg class="w-10 h-10 animate-[bounce_1s_ease-in-out_2.5]" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-xs font-black text-emerald-600 uppercase tracking-widest block">Application
                Authenticated</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Request Successfully
                Lodged</h1>
        </div>

        {{-- Ticket Design Card --}}
        <div class="ticket-card px-8 py-8 mb-8">
            {{-- Ticket Header Section --}}
            <div class="text-center">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Secure Tracking
                    Identifier</span>

                {{-- Tracking code display with Alpine interactive copy --}}
                <div x-data="{ copied: false }" class="relative max-w-sm mx-auto">
                    <div
                        class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 group">
                        <span
                            class="font-mono text-xl sm:text-2xl font-extrabold tracking-widest text-slate-900 block truncate select-all">{{ $docRequest->tracking_code }}</span>

                        <button
                            @click="navigator.clipboard.writeText('{{ $docRequest->tracking_code }}'); copied = true; setTimeout(() => copied = false, 2500)"
                            class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition-all flex-shrink-0 relative"
                            title="Copy secure code">
                            <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="copied" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>

                            {{-- Tooltip feedback --}}
                            <span x-show="copied" x-transition
                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 rounded bg-slate-900 text-white text-[10px] font-bold whitespace-nowrap shadow-md">Copied
                                Identifier!</span>
                        </button>
                    </div>
                </div>

                <p class="text-slate-400 text-xs mt-3 max-w-xs mx-auto leading-relaxed">
                    Please retain this exact token. It acts as your primary look-up credential to query processing
                    timeline progress.
                </p>
            </div>

            <div class="ticket-divider"></div>

            {{-- Receipt Summary List --}}
            <div class="space-y-3.5 text-xs">
                <div class="flex justify-between items-start gap-4">
                    <span class="text-slate-400 font-medium">Requested Document</span>
                    <span class="font-bold text-slate-800 text-right">{{ $docRequest->documentType->name }}</span>
                </div>

                <div class="flex justify-between items-start gap-4">
                    <span class="text-slate-400 font-medium">Submission Timestamp</span>
                    <span
                        class="font-bold text-slate-800 text-right">{{ $docRequest->created_at->format('M d, Y h:i A') }}</span>
                </div>

                <div class="flex justify-between items-start gap-4">
                    <span class="text-slate-400 font-medium">Processing Standard</span>
                    <span class="font-bold text-slate-800 text-right">{{ $docRequest->documentType->processing_days }}
                        {{ Str::plural('day', $docRequest->documentType->processing_days) }}</span>
                </div>

                <div class="flex justify-between items-start gap-4 pt-3 border-t border-slate-100">
                    <span class="text-slate-400 font-medium">Applicable Fee</span>
                    <span
                        class="font-extrabold text-slate-900 text-right text-sm">₱{{ number_format($docRequest->documentType->fee, 2) }}</span>
                </div>
            </div>

            {{-- Step checklist footer inside receipt --}}
            <div class="mt-6 pt-4 border-t border-slate-100 bg-slate-50 -mx-8 -mb-8 p-6 text-center">
                <span class="text-[10px] font-extrabold text-slate-400 block uppercase tracking-wider mb-2">Next
                    Verification Lifecycle</span>
                <div class="flex items-center justify-center gap-2 text-xs font-medium text-slate-600">
                    <span class="text-amber-500 font-bold">1. Admin Verification</span>
                    <span class="text-slate-300">→</span>
                    <span>2. Processing </span>
                    <span class="text-slate-300">→</span>
                    <span>3. Release Claim</span>
                </div>
            </div>
        </div>

        {{-- Direct Action Calls --}}
        <div class="space-y-3">
            <a href="{{ route('public.track', ['tracking_code' => $docRequest->tracking_code]) }}"
                class="w-full py-4 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 shadow-xl shadow-brand-500/20 hover:shadow-brand-500/30 transition-all text-center uppercase tracking-wider block">
                Monitor Live Status Timeline
            </a>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('public.request') }}"
                    class="py-3 rounded-xl font-bold text-xs text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-center block">
                    Submit Another
                </a>
                <a href="{{ route('public.home') }}"
                    class="py-3 rounded-xl font-bold text-xs text-slate-500 hover:text-slate-800 transition-colors text-center block">
                    Return to Home
                </a>
            </div>
        </div>
    </main>
@endsection