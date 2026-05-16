@extends('layouts.public')

@section('title', 'Track Document Request Status | VistáBarangay')

@section('content')
    <style>
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
    {{-- Main Content Container --}}
    <div class="flex-1 max-w-3xl w-full mx-auto px-6 py-12">
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
            <div x-data="{ showModal: true }"
                 x-show="showModal"
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
                 style="display: none;"
                 x-cloak>
                
                {{-- Backdrop --}}
                <div x-show="showModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                     @click="showModal = false"></div>
                     
                {{-- Modal Container --}}
                <div x-show="showModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                     
                     {{-- Close button --}}
                     <button @click="showModal = false" class="absolute top-4 right-4 z-50 w-8 h-8 flex items-center justify-center rounded-full bg-black/10 hover:bg-black/20 text-white transition-colors backdrop-blur-md">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                     </button>
                     
                     <div class="overflow-y-auto">
                        @if($docRequest)
                            <div class="bg-white">
                                {{-- Status Dynamic Header Banner --}}
                                @php
                                    $statusMeta = match($docRequest->status) {
                                        'pending'         => ['bg' => 'bg-amber-500', 'badge' => 'bg-amber-600', 'text' => 'Pending Staff Review', 'desc' => 'Your application has been logged safely. Administrators are querying identity proofs prior to acceptance.', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                                        'processing'      => ['bg' => 'bg-brand-500', 'badge' => 'bg-brand-600', 'text' => 'In Active Processing', 'desc' => 'Your document records are currently being drafted and undergoing final signature validation.', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'],
                                        'ready_to_pickup' => ['bg' => 'bg-indigo-500', 'badge' => 'bg-indigo-600', 'text' => 'Ready for Pickup', 'desc' => 'Your document is printed and ready! Please visit the Barangay Hall to claim it. An email has been sent to your address.', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>'],
                                        'released'        => ['bg' => 'bg-emerald-500', 'badge' => 'bg-emerald-600', 'text' => 'Document Released', 'desc' => 'Authentication finished successfully! This document has been claimed.', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                                        'rejected'        => ['bg' => 'bg-rose-500', 'badge' => 'bg-rose-600', 'text' => 'Application Rejected', 'desc' => 'The submission could not proceed. Review the mandatory administrative reasoning stated below.', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'],
                                        default           => ['bg' => 'bg-slate-600', 'badge' => 'bg-slate-700', 'text' => 'Status Discrepancy', 'desc' => 'Unable to determine lifecycle mapping.', 'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
                                    };
                                @endphp

                    <div class="{{ $statusMeta['bg'] }} text-white p-6 sm:p-8 relative overflow-hidden">
                        {{-- Background blur glow --}}
                        <div class="absolute right-0 top-0 w-48 h-48 bg-white/10 rounded-full blur-xl"></div>
                        
                        <div class="relative z-10 flex items-start gap-4">
                            <span class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur-md border border-white/10 flex items-center justify-center flex-shrink-0 shadow-inner">
                                {!! $statusMeta['icon'] !!}
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
                                $lifecycleSteps = ['pending', 'processing', 'ready_to_pickup', 'released'];
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
                                    <span class="text-[10px] font-bold mt-1.5 uppercase tracking-wider text-center max-w-[60px] leading-tight {{ $idx <= $activeIndex ? 'text-slate-800' : 'text-slate-400' }}">
                                        {{ str_replace('_', ' ', $stepName) }}
                                    </span>
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
                        </div>
                        @else
                            {{-- Not Found Card --}}
                            <div class="bg-white p-10 text-center relative">
                                <div class="w-20 h-20 bg-rose-50 rounded-full border border-rose-100 flex items-center justify-center mx-auto mb-5 text-rose-500 shadow-inner">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="font-extrabold text-xl text-slate-900 mb-2">Token Code Not Found</h3>
                                <p class="text-sm text-slate-500 leading-relaxed mb-8 max-w-sm mx-auto">
                                    We could not match the identifier provided against our validated datastore records. Make sure typed characters exactly reflect your receipt text.
                                </p>
                                <button @click="showModal = false" class="px-6 py-3 rounded-xl font-bold text-sm bg-slate-900 text-white hover:bg-slate-800 transition-colors inline-block w-full sm:w-auto shadow-md">
                                    Close & Try Again
                                </button>
                            </div>
                        @endif
                     </div>
                </div>
            </div>
        @endisset

        {{-- Help Guidance link bottom --}}
        <div class="text-center mt-12">
            <a href="{{ route('public.home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">
                ← Return to Barangay Homepage
            </a>
        </div>

    </div>
@endsection
