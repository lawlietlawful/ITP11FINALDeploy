@extends('layouts.app')
@section('content')

<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between" x-data="{ createModal: {{ $errors->any() ? 'true' : 'false' }} }">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Document Types</h1>
        <p class="text-sm text-gray-400 mt-1">Manage available barangay documents</p>
    </div>
    <button @click="createModal = true" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Document Type
    </button>

    {{-- Create Document Type Modal --}}
    <template x-teleport="body">
        <div x-show="createModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50" @click="createModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="createModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-100">
                    <form method="POST" action="{{ route('document-types.store') }}" class="flex flex-col h-full">
                        @csrf
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="text-lg leading-6 font-bold text-gray-900">Add Document Type</h3>
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

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Document Name <span class="text-red-400">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input w-full" placeholder="e.g. Barangay Clearance">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Category <span class="text-red-400">*</span></label>
                                    <select name="category" required class="form-input w-full">
                                        <option value="Clearance" {{ old('category') == 'Clearance' ? 'selected' : '' }}>Clearance</option>
                                        <option value="Certificate" {{ old('category') == 'Certificate' ? 'selected' : '' }}>Certificate</option>
                                        <option value="Permit" {{ old('category') == 'Permit' ? 'selected' : '' }}>Permit</option>
                                        <option value="ID" {{ old('category') == 'ID' ? 'selected' : '' }}>ID</option>
                                        <option value="Other" {{ old('category', 'Other') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Description</label>
                                <textarea name="description" rows="3" class="form-input w-full resize-none" placeholder="Brief description of the document...">{{ old('description') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Requirements</label>
                                <textarea name="requirements" rows="2" class="form-input w-full resize-none" placeholder="e.g. Valid ID, Proof of Residency...">{{ old('requirements') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Fee (₱) <span class="text-red-400">*</span></label>
                                    <input type="number" name="fee" value="{{ old('fee', '0.00') }}" step="0.01" min="0" required class="form-input w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Processing Days <span class="text-red-400">*</span></label>
                                    <input type="number" name="processing_days" value="{{ old('processing_days', 1) }}" min="1" required class="form-input w-full">
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 modal-actions">
                            <button @click="createModal = false" type="button" class="btn-secondary">Cancel</button>
                            <button type="submit" class="btn-primary">Save Document Type</button>
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
                <p class="overview-card-kicker">Total Types</p>
                <p class="overview-card-value mt-1">{{ number_format($stats['total']) }}</p>
                <p class="overview-card-caption text-gray-400">All created documents</p>
            </div>
            <div class="overview-card-icon overview-card-icon-violet">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="stat-card overview-card">
        <div class="overview-card-body">
            <div class="min-w-0 flex-1">
                <p class="overview-card-kicker">Active</p>
                <p class="overview-card-value mt-1">{{ number_format($stats['active']) }}</p>
                <p class="overview-card-caption text-emerald-500">Currently available</p>
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
                <p class="overview-card-kicker">Most Requested</p>
                <p class="text-xl font-extrabold text-gray-900 mt-1 truncate max-w-[180px]" title="{{ $stats['most_requested'] }}">{{ $stats['most_requested'] }}</p>
                <p class="overview-card-caption text-blue-500">Highest volume</p>
            </div>
            <div class="overview-card-icon overview-card-icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>
    </div>
    <div class="stat-card overview-card">
        <div class="overview-card-body">
            <div class="min-w-0 flex-1">
                <p class="overview-card-kicker">Total Revenue</p>
                <p class="text-2xl font-extrabold text-gray-900 mt-1">₱{{ number_format($stats['total_revenue'], 2) }}</p>
                <p class="overview-card-caption text-amber-500">From released docs</p>
            </div>
            <div class="overview-card-icon overview-card-icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
    <form method="GET" action="{{ route('document-types.index') }}" x-ref="filterForm" class="flex flex-wrap gap-3 items-end w-full">
        <div class="relative flex-1 min-w-[220px]">
            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Search Document Type</label>
            <svg class="w-4 h-4 absolute left-3.5 bottom-[11px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" x-model="search"
                   @input="if(search.length >= 1 || search.length === 0) submitForm()"
                   placeholder="Search by name..."
                   class="form-input w-full pl-10 pr-4">
        </div>
        <div class="w-full sm:w-[150px]">
            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status" @change="submitForm()" class="form-input w-full text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        @if(request('search') || request('status'))
            <a href="{{ route('document-types.index') }}" class="btn-secondary h-[38px] flex items-center">Clear</a>
        @endif
    </form>
</div>

<div class="glass-card table-shell">
    <table class="w-full text-sm data-table">
        <thead>
            <tr class="bg-gray-50/40">
                <th class="px-4 py-3 w-10 text-center"></th>
                <th class="px-6 py-3 text-left">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                        Name
                        @if(request('sort', 'created_at') == 'name')
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                        @endif
                    </a>
                </th>
                <th class="px-6 py-3 text-left">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'fee', 'direction' => request('sort') == 'fee' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                        Fee
                        @if(request('sort') == 'fee')
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                        @endif
                    </a>
                </th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Requests</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Revenue</th>
                <th class="px-6 py-3 text-left">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'processing_days', 'direction' => request('sort') == 'processing_days' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                        Processing Days
                        @if(request('sort') == 'processing_days')
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                        @endif
                    </a>
                </th>
                <th class="px-6 py-3 text-left">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'is_active', 'direction' => request('sort') == 'is_active' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 text-[11px] font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                        Status
                        @if(request('sort') == 'is_active')
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                        @endif
                    </a>
                </th>
                <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50/80" 
               x-data="{ 
                   initSortable() {
                       if (typeof Sortable === 'undefined') return;
                       new Sortable(this.$el, {
                           handle: '.drag-handle',
                           animation: 150,
                           ghostClass: 'bg-gray-50',
                           onEnd: (evt) => {
                               let order = [];
                               this.$el.querySelectorAll('.sortable-item').forEach(el => {
                                   order.push(el.dataset.id);
                               });
                               
                               fetch('{{ route('document-types.reorder') }}', {
                                   method: 'POST',
                                   headers: {
                                       'Content-Type': 'application/json',
                                       'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                   },
                                   body: JSON.stringify({ order: order })
                               });
                           }
                       });
                   }
               }" 
               x-init="$nextTick(() => initSortable())">
            @forelse($types as $type)
            <tr class="table-row sortable-item" data-id="{{ $type->id }}" x-data="{ editModal: false, deleteModal: false }">
                <td class="px-4 py-3 text-center text-gray-400 hover:text-gray-600 cursor-move drag-handle" title="Drag to reorder">
                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                </td>
                <td class="px-6 py-3.5">
                    <div class="font-medium text-gray-900">{{ $type->name }}</div>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide {{ $type->category_badge }}">{{ $type->category }}</span>
                </td>
                <td class="px-6 py-3.5 text-gray-700 font-medium">₱{{ number_format($type->fee, 2) }}</td>
                <td class="px-6 py-3.5">
                    <div class="text-gray-600 font-medium">{{ number_format($type->total_requests) }}</div>
                    @if($type->last_requested_at)
                        <div class="text-[10px] text-gray-400 mt-0.5" title="{{ \Carbon\Carbon::parse($type->last_requested_at)->format('M d, Y h:i A') }}">
                            {{ \Carbon\Carbon::parse($type->last_requested_at)->diffForHumans() }}
                        </div>
                    @else
                        <div class="text-[10px] text-gray-400 mt-0.5">Never requested</div>
                    @endif
                </td>
                <td class="px-6 py-3.5 text-emerald-600 font-medium">₱{{ number_format($type->total_revenue, 2) }}</td>
                <td class="px-6 py-3.5 text-gray-600">{{ $type->processing_days }} {{ Str::plural('day', $type->processing_days) }}</td>
                <td class="px-6 py-3.5">
                    <form action="{{ route('document-types.toggle', $type) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 {{ $type->is_active ? 'bg-green-100/80 text-green-800 hover:bg-green-200/80 focus:ring-green-500' : 'bg-gray-100/80 text-gray-600 hover:bg-gray-200/80 focus:ring-gray-500' }}" title="Click to toggle status">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                </td>
                <td class="px-6 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" @click="editModal = true" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50/80 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" @click="deleteModal = true" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50/80 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>

                    <template x-teleport="body">
                        {{-- Edit Modal --}}
                        <div x-show="editModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="editModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50" @click="editModal = false"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                <div x-show="editModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-100">
                                    <form method="POST" action="{{ route('document-types.update', $type) }}" class="flex flex-col h-full">
                                        @csrf @method('PUT')
                                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                            <h3 class="text-lg leading-6 font-bold text-gray-900">Edit Document Type</h3>
                                            <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        <div class="px-6 py-6 space-y-5 text-left">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Document Name <span class="text-red-400">*</span></label>
                                                    <input type="text" name="name" value="{{ $type->name }}" required class="form-input w-full">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Category <span class="text-red-400">*</span></label>
                                                    <select name="category" required class="form-input w-full">
                                                        <option value="Clearance" {{ $type->category == 'Clearance' ? 'selected' : '' }}>Clearance</option>
                                                        <option value="Certificate" {{ $type->category == 'Certificate' ? 'selected' : '' }}>Certificate</option>
                                                        <option value="Permit" {{ $type->category == 'Permit' ? 'selected' : '' }}>Permit</option>
                                                        <option value="ID" {{ $type->category == 'ID' ? 'selected' : '' }}>ID</option>
                                                        <option value="Other" {{ $type->category == 'Other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Description</label>
                                                <textarea name="description" rows="3" class="form-input w-full resize-none">{{ $type->description }}</textarea>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Requirements</label>
                                                <textarea name="requirements" rows="2" class="form-input w-full resize-none" placeholder="e.g. Valid ID, Proof of Residency...">{{ $type->requirements }}</textarea>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Fee (₱) <span class="text-red-400">*</span></label>
                                                    <input type="number" name="fee" value="{{ $type->fee }}" step="0.01" min="0" required class="form-input w-full">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Processing Days <span class="text-red-400">*</span></label>
                                                    <input type="number" name="processing_days" value="{{ $type->processing_days }}" min="1" required class="form-input w-full">
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                <label class="text-sm font-medium text-gray-600">Active</label>
                                            </div>
                                        </div>
                                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 modal-actions">
                                            <button @click="editModal = false" type="button" class="btn-secondary">Cancel</button>
                                            <button type="submit" class="btn-primary">Save Changes</button>
                                        </div>
                                    </form>
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
                                <div x-show="deleteModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100 p-6">
                                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900">Delete Document Type</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Are you sure you want to delete <span class="font-bold text-gray-700">{{ $type->name }}</span>?</p>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-center gap-3 modal-actions">
                                        <button @click="deleteModal = false" type="button" class="btn-secondary flex-1 justify-center">Cancel</button>
                                        <form action="{{ route('document-types.destroy', $type) }}" method="POST" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger w-full">
                                                Delete Document Type
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
            <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400">No document types found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $types->links() }}</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection
