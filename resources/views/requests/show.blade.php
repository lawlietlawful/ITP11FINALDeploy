@extends('layouts.app')
@section('content')

<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('requests.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1 mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Requests
        </a>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Request #{{ $request_item->id }}</h1>
            <span class="whitespace-nowrap px-3 py-1 rounded-full text-[11px] font-semibold {{ $request_item->status_badge }}">{{ Str::title(str_replace('_', ' ', $request_item->status)) }}</span>
        </div>
    </div>

    {{-- Request Details Card --}}
    <div class="glass-card p-6 mb-6">
        <h2 class="text-[15px] font-semibold text-gray-900 mb-4">Request Details</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Tracking Code</span>
                <p class="mt-1 font-bold text-primary-700 font-mono">{{ $request_item->tracking_code ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Resident</span>
                <p class="mt-1 font-medium text-gray-900">{{ $request_item->resident->full_name }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Document Type</span>
                <p class="mt-1 font-medium text-gray-900">{{ $request_item->documentType->name }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Fee</span>
                <p class="mt-1 text-gray-700 font-medium">₱{{ number_format($request_item->documentType->fee, 2) }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Status</span>
                <p class="mt-1">
                    <span class="whitespace-nowrap px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $request_item->status_badge }}">{{ Str::title(str_replace('_', ' ', $request_item->status)) }}</span>
                </p>
            </div>
            <div class="sm:col-span-2">
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Purpose</span>
                <p class="mt-1 text-gray-700">{{ $request_item->purpose }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Date Filed</span>
                <p class="mt-1 text-gray-700">{{ $request_item->created_at->format('M d, Y h:i A') }}</p>
            </div>
            @if($request_item->processedBy)
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Processed By</span>
                <p class="mt-1 text-gray-700">{{ $request_item->processedBy->name }}</p>
            </div>
            @endif
            @if($request_item->released_at)
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Released Date</span>
                <p class="mt-1 text-gray-700">{{ $request_item->released_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif
            @if($request_item->rejection_reason)
            <div class="sm:col-span-2">
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Rejection Reason</span>
                <p class="mt-1 text-red-600">{{ $request_item->rejection_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="glass-card p-6">
        <h2 class="text-[15px] font-semibold text-gray-900 mb-4">Actions</h2>
        <div class="flex flex-wrap gap-3">

            @if($request_item->status === 'pending')
            <form action="{{ route('requests.approve', $request_item) }}" method="POST">
                @csrf
                <button class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Approve
                </button>
            </form>

            <form action="{{ route('requests.reject', $request_item) }}" method="POST" class="flex gap-2 items-start">
                @csrf
                <input name="rejection_reason" placeholder="Rejection reason..." required
                       class="form-input w-64">
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 shadow-lg shadow-red-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject
                </button>
            </form>
            @endif

            @if($request_item->status === 'processing')
            <form action="{{ route('requests.release', $request_item) }}" method="POST">
                @csrf
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-lg shadow-emerald-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mark as Released
                </button>
            </form>
            @endif

            @if($request_item->status === 'released')
            <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50/80 px-4 py-2.5 rounded-xl backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">This document has been released.</span>
            </div>
            @endif

            @if($request_item->status === 'rejected')
            <div class="flex items-center gap-2 text-red-600 bg-red-50/80 px-4 py-2.5 rounded-xl backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">This request was rejected.</span>
            </div>
            @endif


            <a href="{{ route('requests.index') }}" class="btn-secondary">Back to List</a>
        </div>
    </div>

    {{-- Security Info Card --}}
    @if($request_item->ip_address)
    <div class="glass-card p-6 mb-6">
        <h2 class="text-[15px] font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Security Info
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Submitted from IP</span>
                <p class="mt-1 font-mono text-gray-700">{{ $request_item->ip_address }}</p>
            </div>
            @if($request_item->user_agent)
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">User Agent</span>
                <p class="mt-1 text-gray-700 text-xs truncate" title="{{ $request_item->user_agent }}">{{ Str::limit($request_item->user_agent, 80) }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Audit Trail Card --}}
    @if(isset($auditLogs) && $auditLogs->count())
    <div class="glass-card p-6">
        <h2 class="text-[15px] font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Activity Log
        </h2>
        <div class="space-y-3">
            @foreach($auditLogs as $log)
            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50/80 border border-gray-100">
                {{-- Timeline dot --}}
                <div class="mt-1 w-2.5 h-2.5 rounded-full flex-shrink-0 {{ match($log->action) {
                    'created' => 'bg-blue-500',
                    'approved' => 'bg-emerald-500',
                    'released' => 'bg-green-500',
                    'rejected' => 'bg-red-500',
                    'deleted' => 'bg-gray-500',
                    default => 'bg-gray-400',
                } }}"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $log->action_badge }}">{{ strtoupper($log->action) }}</span>
                        @if($log->old_status && $log->new_status)
                            <span class="text-[11px] text-gray-400">{{ $log->old_status }} → {{ $log->new_status }}</span>
                        @elseif($log->new_status)
                            <span class="text-[11px] text-gray-400">→ {{ $log->new_status }}</span>
                        @endif
                    </div>
                    @if($log->description)
                        <p class="text-xs text-gray-600 mt-1">{{ $log->description }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-1.5 text-[11px] text-gray-400">
                        <span>{{ $log->created_at->format('M d, Y h:i A') }}</span>
                        @if($log->user)
                            <span>by {{ $log->user->name }}</span>
                        @else
                            <span>by System (Public)</span>
                        @endif
                        @if($log->ip_address)
                            <span class="font-mono">{{ $log->ip_address }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@endsection

