@extends('layouts.app')
@section('content')

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Reports</h1>
        <p class="text-sm text-gray-400 mt-1">Filter and view document request reports</p>
    </div>

    {{-- Filter Panel --}}
    <div class="glass-card p-5 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Search Resident</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name..."
                       class="form-input w-full">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
                <select name="status" class="form-input w-full">
                    <option value="">All</option>
                    @foreach(['pending','processing','released','rejected'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Document Type</label>
                <select name="document_type_id" class="form-input w-full">
                    <option value="">All Types</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="form-input w-full">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="form-input w-full">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex gap-3">
                <button type="submit" class="btn-primary">Apply Filters</button>
                @if(request()->hasAny(['search','status','document_type_id','date_from','date_to']))
                    <a href="{{ route('reports.index') }}" class="btn-secondary">Clear Filters</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Count --}}
    <p class="text-sm text-gray-400 mb-3">Showing <strong class="text-gray-600">{{ $requests->count() }}</strong> of <strong class="text-gray-600">{{ $requests->total() }}</strong> results</p>

    {{-- Table --}}
    <div class="glass-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/40">
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Resident</th>
                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Document</th>
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
                    <td class="px-6 py-3.5 text-gray-600 max-w-[180px] truncate">{{ $req->purpose }}</td>
                    <td class="px-6 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $req->status_badge }}">{{ ucfirst($req->status) }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-gray-600">{{ $req->processedBy->name ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-gray-400">{{ $req->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No records match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $requests->links() }}</div>
</div>

@endsection
