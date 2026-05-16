@extends('layouts.app')
@section('content')

<div x-data="{ createModal: {{ $errors->any() ? 'true' : 'false' }}, highlightedRequestModal: {{ $highlightedRequest ? 'true' : 'false' }} }">
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Document Requests</h1>
            <p class="text-sm text-gray-400 mt-1">Track and manage all document requests</p>
        </div>
        <button @click="createModal = true" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Request
        </button>

        {{-- Create Request Modal --}}
        <template x-teleport="body">
            <div x-show="createModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="createModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50" @click="createModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="createModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-100">
                        <form method="POST" action="{{ route('requests.store') }}" x-data="{ purpose: '' }" class="flex flex-col h-full">
                            @csrf
                            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="text-lg leading-6 font-bold text-gray-900">New Document Request</h3>
                                <button type="button" @click="createModal = false" class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="px-6 py-6 space-y-5 text-left">
                                {{-- Validation Errors --}}
                                @if($errors->any())
                                    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Resident <span class="text-red-400">*</span></label>
                                <select name="resident_id" required class="form-input w-full">
                                    <option value="">Select a resident...</option>
                                    @if(isset($residents))
                                        @foreach($residents as $resident)
                                            <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                                {{ $resident->last_name }}, {{ $resident->first_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Document Type <span class="text-red-400">*</span></label>
                                <select name="document_type_id" required class="form-input w-full">
                                    <option value="">Select document type...</option>
                                    @if(isset($documentTypes))
                                        @foreach($documentTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }} (₱{{ number_format($type->fee, 2) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Purpose <span class="text-red-400">*</span></label>
                                <textarea name="purpose" rows="3" required x-model="purpose" x-init="purpose = $el.value" class="form-input w-full resize-none" placeholder="State the purpose of this document request...">{{ old('purpose') }}</textarea>
                                <div class="flex flex-wrap gap-1.5 mt-2.5 items-center">
                                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mr-1">Quick-fill:</span>
                                    <button type="button" @click="purpose = 'For Employment / Job Application'" class="px-2.5 py-1 rounded-lg bg-gray-50 hover:bg-primary-50 text-gray-600 hover:text-primary-700 border border-gray-200/60 hover:border-primary-100 text-[11px] font-medium transition-all">Employment</button>
                                    <button type="button" @click="purpose = 'Scholarship Application Requirement'" class="px-2.5 py-1 rounded-lg bg-gray-50 hover:bg-primary-50 text-gray-600 hover:text-primary-700 border border-gray-200/60 hover:border-primary-100 text-[11px] font-medium transition-all">Scholarship</button>
                                    <button type="button" @click="purpose = 'Bank Account Opening / Requirement'" class="px-2.5 py-1 rounded-lg bg-gray-50 hover:bg-primary-50 text-gray-600 hover:text-primary-700 border border-gray-200/60 hover:border-primary-100 text-[11px] font-medium transition-all">Bank Req.</button>
                                    <button type="button" @click="purpose = 'General Identification / Personal Reference'" class="px-2.5 py-1 rounded-lg bg-gray-50 hover:bg-primary-50 text-gray-600 hover:text-primary-700 border border-gray-200/60 hover:border-primary-100 text-[11px] font-medium transition-all">Identification</button>
                                </div>
                            </div>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 modal-actions">
                                <button @click="createModal = false" type="button" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-primary">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

{{-- Analytics Summary Cards --}}
<div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 xl:grid-cols-4">
    <div class="stat-card overview-card">
        <div class="overview-card-body">
            <div class="min-w-0 flex-1">
                <p class="overview-card-kicker">Pending Requests</p>
                <p class="overview-card-value mt-1">{{ number_format($stats['pending']) }}</p>
                <p class="overview-card-caption text-amber-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Requires action
                </p>
            </div>
            <div class="overview-card-icon overview-card-icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="stat-card overview-card">
        <div class="overview-card-body">
            <div class="min-w-0 flex-1">
                <p class="overview-card-kicker">Processing</p>
                <p class="overview-card-value mt-1">{{ number_format($stats['processing']) }}</p>
                <p class="overview-card-caption text-blue-500">Currently drafted</p>
            </div>
            <div class="overview-card-icon overview-card-icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>
    </div>
    <div class="stat-card overview-card">
        <div class="overview-card-body">
            <div class="min-w-0 flex-1">
                <p class="overview-card-kicker">Released This Month</p>
                <p class="overview-card-value mt-1">{{ number_format($stats['released_month']) }}</p>
                <p class="overview-card-caption text-emerald-500">Successfully served</p>
            </div>
            <div class="overview-card-icon overview-card-icon-emerald">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="stat-card overview-card">
        <div class="overview-card-body">
            <div class="min-w-0 flex-1">
                <p class="overview-card-kicker">Total Volume</p>
                <p class="overview-card-value mt-1">{{ number_format($stats['total']) }}</p>
                <p class="overview-card-caption text-gray-400">Lifetime requests</p>
            </div>
            <div class="overview-card-icon overview-card-icon-violet">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Advanced Search & Filters --}}
<div class="mb-5" x-data="{
    search: '{{ request('search') }}',
    debounceTimer: null,
    submitForm() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.$refs.filterForm.submit();
        }, 400);
    }
}">
    <form method="GET" action="{{ route('requests.index') }}" x-ref="filterForm" class="flex flex-wrap gap-3 items-end w-full">
        <div class="relative flex-1 min-w-[220px]">
            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Search Resident</label>
            <svg class="w-4 h-4 absolute left-3.5 bottom-[11px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" x-model="search"
                   @input="if(search.length >= 1 || search.length === 0) submitForm()"
                   placeholder="Search by resident name..."
                   class="form-input w-full pl-10 pr-4">
        </div>
        <div class="w-full sm:w-[150px]">
            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status" @change="submitForm()" class="form-input w-full text-sm">
                <option value="">All Status</option>
                @foreach(['pending','processing','released','rejected'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        @if(request('search') || request('status'))
            <a href="{{ route('requests.index') }}" class="btn-secondary h-[38px] flex items-center">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="glass-card table-shell">
    <table class="w-full text-sm data-table">
        <thead>
            <tr class="bg-gray-50/40">
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">#</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Resident</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Document</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Purpose</th>
                <th class="whitespace-nowrap px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="whitespace-nowrap px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50/80">
            @forelse($requests as $req)
            <tr class="table-row" x-data="{ viewModal: false, deleteModal: false }">
                <td class="px-6 py-3.5 text-gray-400 font-mono text-xs">{{ $req->id }}</td>
                <td class="px-6 py-3.5">
                    <button @click="viewModal = true" class="font-medium text-gray-900 hover:text-primary-600 transition-colors text-left block">
                        {{ $req->resident->full_name }}
                    </button>
                    <span class="block text-[10px] font-mono text-gray-400 mt-0.5">{{ $req->resident->resident_id }}</span>
                </td>
                <td class="px-6 py-3.5 text-gray-600">{{ $req->documentType->name }}</td>
                <td class="px-6 py-3.5 text-gray-600 max-w-[200px] truncate">{{ $req->purpose }}</td>
                <td class="px-6 py-3.5">
                    <span class="whitespace-nowrap px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $req->status_badge }}">{{ Str::title(str_replace('_', ' ', $req->status)) }}</span>
                </td>
                <td class="whitespace-nowrap px-6 py-3.5">
                    <span class="block text-gray-600 font-medium">{{ $req->created_at->format('M d, Y') }}</span>
                    <span class="block text-[10px] text-gray-400 mt-0.5">{{ $req->created_at->format('g:i A') }} • {{ $req->created_at->diffForHumans() }}</span>
                </td>
                <td class="px-6 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" @click="viewModal = true" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50/80 rounded-lg transition-colors" title="View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button type="button" @click="deleteModal = true" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50/80 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>

                    <template x-teleport="body">
                        {{-- View Modal --}}
                        <div x-show="viewModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="viewModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50" @click="viewModal = false"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                <div x-show="viewModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg leading-6 font-bold text-gray-900">Request #{{ $req->id }}</h3>
                                            <span class="whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $req->status_badge }}">{{ Str::title(str_replace('_', ' ', $req->status)) }}</span>
                                        </div>
                                        <button @click="viewModal = false" class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="px-6 py-6">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm mb-8">
                                            <div>
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Tracking Code</span>
                                                <p class="mt-1 font-bold text-primary-700 font-mono">{{ $req->tracking_code ?? '—' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Resident</span>
                                                <p class="mt-1 font-medium text-gray-900">{{ $req->resident->full_name }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Document Type</span>
                                                <p class="mt-1 font-medium text-gray-900">{{ $req->documentType->name }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Fee</span>
                                                <p class="mt-1 text-gray-700 font-medium">₱{{ number_format($req->documentType->fee, 2) }}</p>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Purpose</span>
                                                <p class="mt-1 text-gray-700">{{ $req->purpose }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Date Filed</span>
                                                <p class="mt-1 text-gray-700">{{ $req->created_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                            @if($req->processedBy)
                                            <div>
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Processed By</span>
                                                <p class="mt-1 text-gray-700">{{ $req->processedBy->name }}</p>
                                            </div>
                                            @endif
                                            @if($req->released_at)
                                            <div>
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Released Date</span>
                                                <p class="mt-1 text-gray-700">{{ $req->released_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                            @endif
                                            @if($req->rejection_reason)
                                            <div class="sm:col-span-2">
                                                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Rejection Reason</span>
                                                <p class="mt-1 text-red-600 bg-red-50 p-3 rounded-lg">{{ $req->rejection_reason }}</p>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="pt-5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-white">
                                            <div class="flex flex-wrap items-center gap-3">
                                                @if($req->status === 'pending')
                                                <form action="{{ route('requests.approve', $req) }}" method="POST">
                                                    @csrf
                                                    <button class="btn-primary">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('requests.reject', $req) }}" method="POST" class="flex gap-2 items-center">
                                                    @csrf
                                                    <button class="btn-danger flex-shrink-0">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Reject
                                                    </button>
                                                    <input name="rejection_reason" placeholder="Rejection reason..." required class="form-input w-64">
                                                </form>
                                                @endif
                                                @if($req->status === 'processing')
                                                <form action="{{ route('requests.readyForPickup', $req) }}" method="POST">
                                                    @csrf
                                                    <button class="btn-primary bg-indigo-600 hover:bg-indigo-700 border-indigo-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                        Ready for Pickup (Send Email)
                                                    </button>
                                                </form>
                                                @endif
                                                @if($req->status === 'ready_to_pickup')
                                                <form action="{{ route('requests.release', $req) }}" method="POST">
                                                    @csrf
                                                    <button class="btn-success">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Mark as Released
                                                    </button>
                                                </form>
                                                @endif
                                                @if($req->status === 'released')
                                                <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50/80 px-4 py-2.5 rounded-xl">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-sm font-medium">This document has been released.</span>
                                                </div>
                                                @endif
                                                @if($req->status === 'rejected')
                                                <div class="flex items-center gap-2 text-red-600 bg-red-50/80 px-4 py-2.5 rounded-xl">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-sm font-medium">This request was rejected.</span>
                                                </div>
                                                @endif
                                            </div>
                                            @if(in_array($req->status, ['processing', 'released']))
                                            <a href="{{ route('requests.print', $req) }}" target="_blank" class="btn-secondary inline-flex items-center gap-2 border-gray-200 hover:border-primary-200 text-gray-700 hover:text-primary-700 font-medium">
                                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                Print Document
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-teleport="body">
                        {{-- Delete Modal --}}
                        <div x-show="deleteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="deleteModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50" @click="deleteModal = false"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                <div x-show="deleteModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100 p-6">
                                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900">Delete Request</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Are you sure you want to delete the request from <span class="font-bold text-gray-700">{{ $req->resident->full_name }}</span>? This action cannot be undone.</p>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-center gap-3 modal-actions">
                                        <button @click="deleteModal = false" type="button" class="btn-secondary flex-1 justify-center">Cancel</button>
                                        <form action="{{ route('requests.destroy', $req) }}" method="POST" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger w-full">
                                                Delete Request
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No requests found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $requests->links() }}</div>

@if($highlightedRequest)
    <template x-teleport="body">
        @include('requests.partials.request-details-modal', [
            'documentRequest' => $highlightedRequest,
            'modalState' => 'highlightedRequestModal',
            'closeAction' => "highlightedRequestModal = false; window.history.replaceState({}, '', '" . route('requests.index', request()->except('open_request')) . "')",
        ])
    </template>
@endif

</div>

@endsection
