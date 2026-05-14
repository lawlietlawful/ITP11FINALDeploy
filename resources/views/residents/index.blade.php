@extends('layouts.app')
@section('content')

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Residents</h1>
            <p class="text-sm text-gray-400 mt-1">Manage barangay resident records</p>
        </div>
        <div class="flex flex-wrap items-center gap-3"
            x-data="{ importModal: false, createModal: {{ $errors->any() ? 'true' : 'false' }} }">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('residents.export') }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>
                <button @click="importModal = true" class="btn-secondary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import CSV
                </button>
                <button @click="createModal = true" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Resident
                </button>

                {{-- Create Resident Modal --}}
                <template x-teleport="body">
                    <div x-show="createModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="createModal" x-transition.opacity
                                class="fixed inset-0 transition-opacity bg-gray-900/50" @click="createModal = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                            <div x-show="createModal" x-transition.scale.origin.bottom
                                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                                <form method="POST" action="{{ route('residents.store') }}" class="flex flex-col h-full">
                                    @csrf
                                    <div
                                        class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900">Add New Resident</h3>
                                        <button type="button" @click="createModal = false"
                                            class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="px-6 py-6 space-y-5">
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

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">First Name <span
                                                        class="text-red-400">*</span></label>
                                                <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                                    class="form-input w-full">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Middle
                                                    Name</label>
                                                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                                    class="form-input w-full">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Last Name <span
                                                        class="text-red-400">*</span></label>
                                                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                                    class="form-input w-full">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 mb-1.5">Address (Purok) <span
                                                    class="text-red-400">*</span></label>
                                            <select name="address" required class="form-input w-full cursor-pointer">
                                                <option value="" disabled {{ old('address') ? '' : 'selected' }}>— Select Purok —</option>
                                                @foreach(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5', 'Purok 6'] as $purok)
                                                    <option value="{{ $purok }}" {{ old('address') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Contact
                                                    Number</label>
                                                <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                                                    class="form-input w-full" placeholder="09171234567">
                                                <p class="text-[11px] text-gray-400 mt-1">11 digits starting with 09</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Birthdate</label>
                                                <input type="date" name="birthdate" value="{{ old('birthdate') }}"
                                                    class="form-input w-full" max="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Gender</label>
                                                <select name="gender" class="form-input w-full">
                                                    <option value="">— Select —</option>
                                                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male
                                                    </option>
                                                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>
                                                        Female</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Civil
                                                    Status</label>
                                                <select name="civil_status" class="form-input w-full">
                                                    <option value="">— Select —</option>
                                                    <option value="Single" {{ old('civil_status') === 'Single' ? 'selected' : '' }}>Single</option>
                                                    <option value="Married" {{ old('civil_status') === 'Married' ? 'selected' : '' }}>Married</option>
                                                    <option value="Widowed" {{ old('civil_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                                    <option value="Separated" {{ old('civil_status') === 'Separated' ? 'selected' : '' }}>Separated</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 modal-actions">
                                        <button @click="createModal = false" type="button" class="btn-secondary">Cancel</button>
                                        <button type="submit" class="btn-primary">Save Resident</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Import CSV Modal --}}
                <template x-teleport="body">
                    <div x-show="importModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="importModal" x-transition.opacity
                                class="fixed inset-0 transition-opacity bg-gray-900/50" @click="importModal = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                            <div x-show="importModal" x-transition.scale.origin.bottom
                                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                                <form method="POST" action="{{ route('residents.import') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div
                                        class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                        <div>
                                            <h3 class="text-lg leading-6 font-bold text-gray-900">Import Residents from CSV</h3>
                                            <p class="text-xs text-gray-400 mt-0.5">Upload a .csv file with resident data</p>
                                        </div>
                                        <button type="button" @click="importModal = false"
                                            class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="px-6 py-6 space-y-4">
                                        <div
                                            class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary-300 transition-colors">
                                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <input type="file" name="csv_file" accept=".csv,.txt" required
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                                            <p class="text-[11px] text-gray-400 mt-2">Max file size: 2MB</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                Expected CSV Columns</p>
                                            <p class="text-xs text-gray-500 font-mono leading-relaxed">first_name, middle_name,
                                                last_name, address, contact_number, birthdate, gender, civil_status</p>
                                            <p class="text-[10px] text-gray-400 mt-2">• <strong>Required:</strong> first_name,
                                                last_name, address</p>
                                            <p class="text-[10px] text-gray-400">• Duplicates (same name + address) will be
                                                skipped</p>
                                            <p class="text-[10px] text-gray-400">• Resident IDs are auto-generated</p>
                                        </div>
                                    </div>
                                    <div
                                        class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 modal-actions">
                                        <button @click="importModal = false" type="button" class="btn-secondary">Cancel</button>
                                        <button type="submit" class="btn-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            Import
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            @endif
        </div>
    </div>

    {{-- Search --}}
    {{-- Analytics Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">Total Residents</p>
                    <p class="overview-card-value mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="overview-card-icon overview-card-icon-blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">New This Month</p>
                    <p class="overview-card-value mt-1">{{ number_format($stats['new_month']) }}</p>
                </div>
                <div class="overview-card-icon overview-card-icon-amber">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">Active Residents</p>
                    <p class="overview-card-value mt-1">{{ number_format($stats['active']) }}</p>
                    <p class="overview-card-caption text-gray-400">Requested in last 6 months</p>
                </div>
                <div class="overview-card-icon overview-card-icon-emerald">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card overview-card">
            <div class="overview-card-body">
                <div class="min-w-0 flex-1">
                    <p class="overview-card-kicker">Total Requests</p>
                    <p class="overview-card-value mt-1">{{ number_format($stats['total_requests']) }}</p>
                </div>
                <div class="overview-card-icon overview-card-icon-violet">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
        <form method="GET" action="{{ route('residents.index') }}" x-ref="filterForm">
            <div class="flex flex-wrap gap-3 items-end">
                {{-- Live Search Input --}}
                <div class="relative flex-1 min-w-[220px]">
                    <label
                        class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Search</label>
                    <svg class="w-4 h-4 absolute left-3.5 bottom-[11px] text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" x-model="search"
                        @input="if(search.length >= 1 || search.length === 0) submitForm()"
                        placeholder="Name, address, or resident ID..." class="form-input w-full pl-10 pr-4">
                </div>

                {{-- Gender Filter --}}
                <div class="w-[140px]">
                    <label
                        class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Gender</label>
                    <select name="gender" @change="$refs.filterForm.submit()" class="form-input w-full text-sm">
                        <option value="">All</option>
                        <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                {{-- Civil Status Filter --}}
                <div class="w-[160px]">
                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Civil
                        Status</label>
                    <select name="civil_status" @change="$refs.filterForm.submit()" class="form-input w-full text-sm">
                        <option value="">All</option>
                        <option value="Single" {{ request('civil_status') === 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ request('civil_status') === 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Widowed" {{ request('civil_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Separated" {{ request('civil_status') === 'Separated' ? 'selected' : '' }}>Separated
                        </option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="w-[140px]">
                    <label
                        class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" @change="$refs.filterForm.submit()" class="form-input w-full text-sm">
                        <option value="">All</option>
                        <option value="New" {{ request('status') === 'New' ? 'selected' : '' }}>🟡 New</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>🟢 Active</option>
                        <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>🔴 Inactive</option>
                    </select>
                </div>

                {{-- Clear All --}}
                @if(request('search') || request('gender') || request('civil_status') || request('status'))
                    <a href="{{ route('residents.index') }}" class="btn-secondary h-[42px] flex items-center text-xs">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="glass-card table-shell">
        <table class="w-full text-sm data-table">
            <thead>
                @php
                    $currentSort = request('sort_by');
                    $currentDir = request('sort_dir', 'desc');
                    $sortParams = request()->except(['sort_by', 'sort_dir', 'page']);
                @endphp
                <tr class="bg-gray-50/40">
                    {{-- Name (sortable) --}}
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        <a href="{{ route('residents.index', array_merge($sortParams, ['sort_by' => 'first_name', 'sort_dir' => ($currentSort === 'first_name' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                            class="inline-flex items-center gap-1 hover:text-gray-600 transition-colors">
                            Name
                            @if($currentSort === 'first_name')
                                <svg class="w-3 h-3 {{ $currentDir === 'asc' ? '' : 'rotate-180' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                </svg>
                            @endif
                        </a>
                    </th>

                    {{-- Status (not sortable) --}}
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status
                    </th>

                    {{-- Address (sortable) --}}
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        <a href="{{ route('residents.index', array_merge($sortParams, ['sort_by' => 'address', 'sort_dir' => ($currentSort === 'address' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                            class="inline-flex items-center gap-1 hover:text-gray-600 transition-colors">
                            Address
                            @if($currentSort === 'address')
                                <svg class="w-3 h-3 {{ $currentDir === 'asc' ? '' : 'rotate-180' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                </svg>
                            @endif
                        </a>
                    </th>

                    {{-- Contact (sortable) --}}
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        <a href="{{ route('residents.index', array_merge($sortParams, ['sort_by' => 'contact_number', 'sort_dir' => ($currentSort === 'contact_number' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                            class="inline-flex items-center gap-1 hover:text-gray-600 transition-colors">
                            Contact
                            @if($currentSort === 'contact_number')
                                <svg class="w-3 h-3 {{ $currentDir === 'asc' ? '' : 'rotate-180' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                </svg>
                            @endif
                        </a>
                    </th>

                    {{-- Age (sortable via birthdate) --}}
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        <a href="{{ route('residents.index', array_merge($sortParams, ['sort_by' => 'birthdate', 'sort_dir' => ($currentSort === 'birthdate' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                            class="inline-flex items-center gap-1 hover:text-gray-600 transition-colors">
                            Age
                            @if($currentSort === 'birthdate')
                                <svg class="w-3 h-3 {{ $currentDir === 'asc' ? '' : 'rotate-180' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                </svg>
                            @endif
                        </a>
                    </th>

                    {{-- Requests (sortable) --}}
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        <a href="{{ route('residents.index', array_merge($sortParams, ['sort_by' => 'document_requests_count', 'sort_dir' => ($currentSort === 'document_requests_count' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                            class="inline-flex items-center gap-1 hover:text-gray-600 transition-colors">
                            Requests
                            @if($currentSort === 'document_requests_count')
                                <svg class="w-3 h-3 {{ $currentDir === 'asc' ? '' : 'rotate-180' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                </svg>
                            @endif
                        </a>
                    </th>

                    {{-- Actions (not sortable) --}}
                    <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50/80">
                @forelse($residents as $resident)
                    <tr class="table-row" x-data="{ viewModal: false, editModal: false, deleteModal: false }">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-1.5">
                                <button @click="viewModal = true"
                                    class="font-medium text-gray-900 hover:text-primary-600 transition-colors text-left">{{ $resident->full_name }}</button>
                            </div>
                            <span class="block text-[10px] font-mono text-gray-400 mt-0.5">{{ $resident->resident_id }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $resident->status_color['bg'] }} {{ $resident->status_color['text'] }} {{ $resident->status_color['border'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $resident->status_color['dot'] }}"></span>
                                {{ $resident->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <a href="{{ route('residents.index', ['search' => $resident->address]) }}"
                                class="text-gray-600 hover:text-primary-600 transition-colors max-w-xs truncate block"
                                title="Click to see all residents at this address">
                                {{ $resident->address }}
                            </a>
                            @if($resident->household_count > 0)
                                <span class="inline-flex items-center gap-1 text-[10px] text-gray-400 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    {{ $resident->household_count }} other member{{ $resident->household_count > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-gray-600">{{ $resident->contact_number ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-gray-600">
                            @if($resident->age !== null)
                                {{ $resident->age }} <span class="text-gray-400 text-xs">yo</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            @if($resident->document_requests_count > 0)
                                <span
                                    class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full text-[11px] font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                    {{ $resident->document_requests_count }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" @click="viewModal = true"
                                    class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50/80 rounded-lg transition-colors"
                                    title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                @if(auth()->user()->role === 'admin')
                                    <button type="button" @click="editModal = true"
                                        class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50/80 rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="deleteModal = true"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50/80 rounded-lg transition-colors"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            <template x-teleport="body">
                                {{-- View Modal --}}
                                <div x-show="viewModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                                    <div
                                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="viewModal" x-transition.opacity
                                            class="fixed inset-0 transition-opacity bg-gray-900/50" @click="viewModal = false">
                                        </div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                        <div x-show="viewModal" x-transition.scale.origin.bottom
                                            class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-100">
                                            <div
                                                class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                                <div>
                                                    <h3 class="text-lg leading-6 font-bold text-gray-900">
                                                        {{ $resident->full_name }}</h3>
                                                    <span
                                                        class="text-[11px] font-mono text-gray-400">{{ $resident->resident_id }}</span>
                                                </div>
                                                <button @click="viewModal = false"
                                                    class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="px-6 py-6">
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mb-6">
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">First
                                                            Name</span>
                                                        <p class="mt-1 text-gray-700 font-medium">{{ $resident->first_name }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Middle
                                                            Name</span>
                                                        <p class="mt-1 text-gray-700">{{ $resident->middle_name ?? '—' }}</p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Last
                                                            Name</span>
                                                        <p class="mt-1 text-gray-700 font-medium">{{ $resident->last_name }}</p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Address</span>
                                                        <p class="mt-1 text-gray-700">{{ $resident->address }}</p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Contact
                                                            Number</span>
                                                        <p class="mt-1 text-gray-700">{{ $resident->contact_number ?? '—' }}</p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Gender</span>
                                                        <p class="mt-1 text-gray-700">{{ $resident->gender ?? '—' }}</p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Civil
                                                            Status</span>
                                                        <p class="mt-1 text-gray-700">{{ $resident->civil_status ?? '—' }}</p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Birthdate</span>
                                                        <p class="mt-1 text-gray-700">
                                                            {{ $resident->birthdate ? $resident->birthdate->format('M d, Y') : '—' }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Age</span>
                                                        <p class="mt-1 text-gray-700 font-bold">
                                                            {{ $resident->age !== null ? $resident->age . ' years old' : '—' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                {{-- Household Members --}}
                                                @php $householdMembers = $resident->householdMembers(); @endphp
                                                @if($householdMembers->count() > 0)
                                                    <div class="border-t border-gray-100/60 pt-4">
                                                        <h4
                                                            class="text-[13px] font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                            </svg>
                                                            Household Members
                                                            <span
                                                                class="text-[10px] text-gray-400 font-normal">({{ $householdMembers->count() }}
                                                                sharing household)</span>
                                                        </h4>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach($householdMembers as $member)
                                                                <div
                                                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-50 border border-gray-100">
                                                                    <div
                                                                        class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-[11px] font-bold text-primary-600">
                                                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-xs font-semibold text-gray-800">
                                                                            {{ $member->full_name }}</p>
                                                                        <p class="text-[10px] text-gray-400">
                                                                            {{ $member->age ? $member->age . ' yrs' : '—' }} ·
                                                                            {{ $member->gender ?? '—' }}</p>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="border-t border-gray-100/60 pt-4">
                                                    <h4
                                                        class="text-[13px] font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Document Timeline
                                                        @if($resident->documentRequests->count() > 0)
                                                            <span
                                                                class="text-[10px] text-gray-400 font-normal">({{ $resident->documentRequests->count() }}
                                                                request{{ $resident->documentRequests->count() > 1 ? 's' : '' }})</span>
                                                        @endif
                                                    </h4>
                                                    @if($resident->documentRequests->count() > 0)
                                                        <div class="relative ml-3">
                                                            {{-- Vertical line --}}
                                                            <div class="absolute left-[7px] top-2 bottom-2 w-[2px] bg-gray-100">
                                                            </div>

                                                            @foreach($resident->documentRequests->sortByDesc('created_at') as $req)
                                                                <div class="relative flex gap-4 pb-5 last:pb-0">
                                                                    {{-- Timeline dot --}}
                                                                    <div class="relative z-10 flex-shrink-0 mt-1">
                                                                        @php
                                                                            $dotColor = match ($req->status) {
                                                                                'completed' => 'bg-emerald-400 ring-emerald-100',
                                                                                'processing' => 'bg-blue-400 ring-blue-100',
                                                                                'pending' => 'bg-amber-400 ring-amber-100',
                                                                                'rejected' => 'bg-red-400 ring-red-100',
                                                                                default => 'bg-gray-300 ring-gray-100',
                                                                            };
                                                                        @endphp
                                                                        <div class="w-4 h-4 rounded-full {{ $dotColor }} ring-4"></div>
                                                                    </div>

                                                                    {{-- Content --}}
                                                                    <div
                                                                        class="flex-1 min-w-0 bg-gray-50/50 rounded-xl px-4 py-3 border border-gray-100/80">
                                                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                                                            <p class="text-sm font-semibold text-gray-900">
                                                                                {{ $req->documentType->name }}</p>
                                                                            <span
                                                                                class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $req->status_badge }}">{{ ucfirst($req->status) }}</span>
                                                                        </div>
                                                                        <div class="flex items-center gap-2 mt-1.5">
                                                                            <span
                                                                                class="text-[11px] text-gray-500">{{ $req->created_at->format('M d, Y') }}</span>
                                                                            <span class="text-[10px] text-gray-400">·</span>
                                                                            <span
                                                                                class="text-[10px] text-gray-400">{{ $req->created_at->diffForHumans() }}</span>
                                                                            @if($req->tracking_code)
                                                                                <span class="text-[10px] text-gray-400">·</span>
                                                                                <span
                                                                                    class="text-[10px] font-mono text-gray-400">{{ $req->tracking_code }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-center py-6">
                                                            <svg class="w-8 h-8 mx-auto text-gray-200 mb-2" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1.5"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <p class="text-sm text-gray-400">No document requests yet.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div
                                                class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end modal-actions">
                                                <button @click="viewModal = false" type="button"
                                                    class="btn-secondary">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            @if(auth()->user()->role === 'admin')
                                <template x-teleport="body">
                                    {{-- Edit Modal --}}
                                    <div x-show="editModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                                        <div
                                            class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="editModal" x-transition.opacity
                                                class="fixed inset-0 transition-opacity bg-gray-900/50" @click="editModal = false">
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                            <div x-show="editModal" x-transition.scale.origin.bottom
                                                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
                                                <form method="POST" action="{{ route('residents.update', $resident) }}"
                                                    class="flex flex-col h-full">
                                                    @csrf @method('PUT')
                                                    <div
                                                        class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                                        <h3 class="text-lg leading-6 font-bold text-gray-900">Edit Resident</h3>
                                                        <button type="button" @click="editModal = false"
                                                            class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="px-6 py-6 space-y-5">
                                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">First
                                                                    Name <span class="text-red-400">*</span></label>
                                                                <input type="text" name="first_name"
                                                                    value="{{ $resident->first_name }}" required
                                                                    class="form-input w-full">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Middle
                                                                    Name</label>
                                                                <input type="text" name="middle_name"
                                                                    value="{{ $resident->middle_name }}" class="form-input w-full">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Last
                                                                    Name <span class="text-red-400">*</span></label>
                                                                <input type="text" name="last_name"
                                                                    value="{{ $resident->last_name }}" required
                                                                    class="form-input w-full">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-600 mb-1.5">Address
                                                                <span class="text-red-400">*</span></label>
                                                            <textarea name="address" rows="3" required
                                                                class="form-input w-full resize-none">{{ $resident->address }}</textarea>
                                                        </div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                            <div>
                                                                <label
                                                                    class="block text-sm font-medium text-gray-600 mb-1.5">Contact
                                                                    Number</label>
                                                                <input type="text" name="contact_number"
                                                                    value="{{ $resident->contact_number }}"
                                                                    class="form-input w-full" placeholder="09171234567">
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-sm font-medium text-gray-600 mb-1.5">Birthdate</label>
                                                                <input type="date" name="birthdate"
                                                                    value="{{ $resident->birthdate?->format('Y-m-d') }}"
                                                                    class="form-input w-full">
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                            <div>
                                                                <label
                                                                    class="block text-sm font-medium text-gray-600 mb-1.5">Gender</label>
                                                                <select name="gender" class="form-input w-full">
                                                                    <option value="">— Select —</option>
                                                                    <option value="Male" {{ $resident->gender === 'Male' ? 'selected' : '' }}>Male</option>
                                                                    <option value="Female" {{ $resident->gender === 'Female' ? 'selected' : '' }}>Female</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Civil
                                                                    Status</label>
                                                                <select name="civil_status" class="form-input w-full">
                                                                    <option value="">— Select —</option>
                                                                    <option value="Single" {{ $resident->civil_status === 'Single' ? 'selected' : '' }}>Single</option>
                                                                    <option value="Married" {{ $resident->civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                                                                    <option value="Widowed" {{ $resident->civil_status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                                                    <option value="Separated" {{ $resident->civil_status === 'Separated' ? 'selected' : '' }}>
                                                                        Separated</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 modal-actions">
                                                        <button @click="editModal = false" type="button"
                                                            class="btn-secondary">Cancel</button>
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
                                        <div
                                            class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="deleteModal" x-transition.opacity
                                                class="fixed inset-0 transition-opacity bg-gray-900/50"
                                                @click="deleteModal = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                            <div x-show="deleteModal" x-transition.scale.origin.bottom
                                                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100 p-6">
                                                <div
                                                    class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="text-lg leading-6 font-bold text-gray-900">Delete Resident</h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500">Are you sure you want to delete <span
                                                                class="font-bold text-gray-700">{{ $resident->full_name }}</span>?
                                                            This will permanently remove their records.</p>
                                                    </div>
                                                </div>
                                                <div class="mt-6 flex justify-center gap-3 modal-actions">
                                                    <button @click="deleteModal = false" type="button"
                                                        class="btn-secondary flex-1 justify-center">Cancel</button>
                                                    <form action="{{ route('residents.destroy', $resident) }}" method="POST"
                                                        class="flex-1">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn-danger w-full">
                                                            Delete Resident
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No residents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $residents->links() }}</div>

@endsection
