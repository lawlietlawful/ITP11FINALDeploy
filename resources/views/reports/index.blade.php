@extends('layouts.app')
@section('content')

@push('styles')
<style>
    @media print {
        @page {
            size: landscape;
            margin: 10mm;
        }
        
        /* Hide non-essential elements */
        aside, nav, header, form, .btn-primary, .btn-secondary, .pagination-container {
            display: none !important;
        }
        
        /* Expand main content and fix clipping */
        body { background-color: white !important; font-size: 11px !important; }
        html, body, .min-h-screen, main, .main-content-area { 
            height: auto !important; 
            min-height: auto !important; 
            overflow: visible !important; 
        }
        
        /* Override sidebar margins and wrappers */
        main, .ml-64, .lg\:ml-64, #main-content, .glass-card, .p-4, .p-5, .p-6, .p-7, .p-8, .main-content-area { 
            margin: 0 !important; 
            padding: 0 !important; 
            width: 100% !important; 
            max-width: 100% !important;
            box-shadow: none !important; 
            border: none !important; 
            background: transparent !important; 
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            table-layout: auto !important;
            border-collapse: collapse !important;
        }

        th, td {
            padding: 8px 4px !important;
            white-space: normal !important;
        }
        
        /* Ensure colors print well */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endpush

<div>
    <div class="mb-6 print:hidden">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Reports</h1>
        <p class="text-sm text-gray-400 mt-1">Filter and view document request reports</p>
    </div>

    {{-- Analytics Cards --}}
    <div class="grid grid-cols-1 gap-4 mb-6 print:hidden sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">Total Requests</p>
                    <p class="overview-card-value mt-1">{{ number_format($stats['total_requests']) }}</p>
                    <p class="overview-card-caption text-gray-400">Filtered total</p>
                </div>
                <div class="overview-card-icon overview-card-icon-blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">Total Revenue</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-1">₱{{ number_format($stats['total_revenue'], 2) }}</p>
                    <p class="overview-card-caption text-emerald-500">Estimated</p>
                </div>
                <div class="overview-card-icon overview-card-icon-emerald">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">Approved / Released</p>
                    <p class="overview-card-value mt-1">{{ number_format($stats['approved']) }}</p>
                    <p class="overview-card-caption text-violet-500">Completed</p>
                </div>
                <div class="overview-card-icon overview-card-icon-violet">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">Pending</p>
                    <p class="overview-card-value mt-1">{{ number_format($stats['pending']) }}</p>
                    <p class="overview-card-caption text-amber-500">Awaiting action</p>
                </div>
                <div class="overview-card-icon overview-card-icon-amber">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Search & Filters --}}
    <div class="mb-5 print:hidden" x-data="{
        search: '{{ request('search') }}',
        debounceTimer: null,
        submitForm() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.$refs.filterForm.submit();
            }, 400);
        }
    }">
        <form method="GET" action="{{ route('reports.index') }}" x-ref="filterForm">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="relative flex-1 min-w-[220px]">
                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Search</label>
                    <svg class="w-4 h-4 absolute left-3.5 bottom-[11px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" x-model="search"
                           @input="if(search.length >= 1 || search.length === 0) submitForm()"
                           placeholder="Name, Tracking Code..."
                           class="form-input w-full pl-10 pr-4">
                </div>
                
                <div class="w-full sm:w-[140px]">
                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" @change="submitForm()" class="form-input w-full text-sm">
                        <option value="">All</option>
                        @foreach(['pending','processing','released','rejected'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full sm:w-[180px]">
                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Document Type</label>
                    <select name="document_type_id" @change="submitForm()" class="form-input w-full text-sm">
                        <option value="">All Types</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full lg:w-auto lg:flex-shrink-0" x-data="{
                    setDate(preset) {
                        const today = new Date();
                        let fromDate = new Date();
                        let toDate = new Date();

                        if (preset === 'today') {
                            // Keep today for both
                        } else if (preset === 'week') {
                            const day = today.getDay();
                            const diff = today.getDate() - day + (day == 0 ? -6 : 1);
                            fromDate = new Date(today.setDate(diff));
                            toDate = new Date();
                        } else if (preset === 'month') {
                            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                            toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        } else if (preset === 'year') {
                            fromDate = new Date(today.getFullYear(), 0, 1);
                            toDate = new Date(today.getFullYear(), 11, 31);
                        }

                        $refs.dateFrom.value = fromDate.toISOString().split('T')[0];
                        $refs.dateTo.value = toDate.toISOString().split('T')[0];
                        submitForm();
                    }
                }">
                    <div class="flex justify-between items-end mb-1.5">
                        <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Date Range</label>
                        <div class="flex gap-1">
                            <button type="button" @click="setDate('today')" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">Today</button>
                            <button type="button" @click="setDate('week')" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">Week</button>
                            <button type="button" @click="setDate('month')" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">Month</button>
                            <button type="button" @click="setDate('year')" class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">Year</button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input type="date" x-ref="dateFrom" name="date_from" value="{{ request('date_from') }}" @change="submitForm()" class="form-input w-full sm:w-[130px] text-sm px-2">
                        <span class="text-gray-400 text-xs">to</span>
                        <input type="date" x-ref="dateTo" name="date_to" value="{{ request('date_to') }}" @change="submitForm()" class="form-input w-full sm:w-[130px] text-sm px-2">
                    </div>
                </div>

                @if(request()->hasAny(['search','status','document_type_id','date_from','date_to']))
                    <a href="{{ route('reports.index') }}" class="btn-secondary h-[42px] flex items-center text-xs">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Header --}}
    <div class="flex flex-col gap-3 mb-3 print:hidden lg:flex-row lg:items-center lg:justify-between">
        <p class="text-sm text-gray-400">Showing <strong class="text-gray-600">{{ $requests->count() }}</strong> of <strong class="text-gray-600">{{ $requests->total() }}</strong> results</p>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reports.export', request()->query()) }}" class="btn-secondary flex items-center gap-2 py-1.5 text-sm" title="Download as CSV">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <button type="button" onclick="window.print()" class="btn-secondary flex items-center gap-2 py-1.5 text-sm" title="Print Report">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Report
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="glass-card table-shell">
        <table class="w-full text-sm data-table">
            <thead>
                <tr class="bg-gray-50/40">
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Resident</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Document</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Fee</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Purpose</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Processed By</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50/80">
                @forelse($requests as $req)
                <tr class="table-row">
                    <td class="px-6 py-3.5 text-gray-400 font-mono text-xs">{{ $req->id }}</td>
                    <td class="px-6 py-3.5 font-medium text-gray-900">{{ $req->resident->full_name }}</td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $req->documentType->name }}</td>
                    <td class="px-6 py-3.5 text-emerald-600 font-medium">₱{{ number_format($req->documentType->fee, 2) }}</td>
                    <td class="px-6 py-3.5 text-gray-600 max-w-[180px] truncate">{{ $req->purpose }}</td>
                    <td class="px-6 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $req->status_badge }}">{{ ucfirst($req->status) }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $req->processedBy->name ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-gray-400">{{ $req->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400">No records match your filters.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t border-gray-100">
                <tr>
                    <td colspan="8" class="px-6 py-4 text-right">
                        <span class="text-sm text-gray-500 mr-3">Filtered Revenue Total:</span>
                        <span class="text-lg font-bold text-emerald-600">₱{{ number_format($stats['total_revenue'], 2) }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-5 pagination-container print:hidden">{{ $requests->links() }}</div>
</div>

@endsection
