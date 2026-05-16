@extends('layouts.app')
@section('content')

<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('residents.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1 mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Residents
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $resident->full_name }}</h1>
        <span class="text-xs font-mono text-gray-400 mt-0.5">{{ $resident->resident_id }}</span>
    </div>

    {{-- Resident Info Card --}}
    <div class="glass-card p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <h2 class="text-[15px] font-semibold text-gray-900">Resident Information</h2>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('residents.edit', $resident) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">First Name</span>
                <p class="mt-1 font-medium text-gray-900">{{ $resident->first_name }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Middle Name</span>
                <p class="mt-1 text-gray-700">{{ $resident->middle_name ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Last Name</span>
                <p class="mt-1 font-medium text-gray-900">{{ $resident->last_name }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Address</span>
                <p class="mt-1 text-gray-700">{{ $resident->address }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Contact Number</span>
                <p class="mt-1 text-gray-700">{{ $resident->contact_number ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Gender</span>
                <p class="mt-1 text-gray-700">{{ $resident->gender ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Civil Status</span>
                <p class="mt-1 text-gray-700">{{ $resident->civil_status ?? '—' }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Birthdate</span>
                <p class="mt-1 text-gray-700">{{ $resident->birthdate ? $resident->birthdate->format('M d, Y') : '—' }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Age</span>
                <p class="mt-1 text-gray-700 font-bold">{{ $resident->age !== null ? $resident->age . ' years old' : '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Household Members --}}
    @php $householdMembers = $resident->householdMembers(); @endphp
    @if($householdMembers->count() > 0)
    <div class="glass-card p-6 mb-6">
        <h2 class="text-[15px] font-semibold text-gray-900 flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Household Members
            <span class="text-[10px] text-gray-400 font-normal">({{ $householdMembers->count() }} sharing household)</span>
        </h2>
        <div class="flex flex-wrap gap-3">
            @foreach($householdMembers as $member)
                <a href="{{ route('residents.show', $member) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-50 border border-gray-100 hover:border-primary-200 hover:bg-primary-50/30 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-[11px] font-bold text-primary-600">
                        {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-800">{{ $member->full_name }}</p>
                        <p class="text-[10px] text-gray-400">{{ $member->age ? $member->age . ' yrs' : '—' }} · {{ $member->gender ?? '—' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Request History Timeline --}}
    <div class="glass-card p-6">
        <h2 class="text-[15px] font-semibold text-gray-900 flex items-center gap-2 mb-5">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Document Timeline
            @if($requests->count() > 0)
                <span class="text-[10px] text-gray-400 font-normal">({{ $requests->count() }} request{{ $requests->count() > 1 ? 's' : '' }})</span>
            @endif
        </h2>

        @if($requests->count() > 0)
        <div class="relative ml-3">
            <div class="absolute left-[7px] top-2 bottom-2 w-[2px] bg-gray-100"></div>

            @foreach($requests->sortByDesc('created_at') as $req)
            <div class="relative flex gap-4 pb-5 last:pb-0">
                <div class="relative z-10 flex-shrink-0 mt-1">
                    @php
                        $dotColor = match($req->status) {
                            'completed' => 'bg-emerald-400 ring-emerald-100',
                            'processing' => 'bg-blue-400 ring-blue-100',
                            'pending' => 'bg-amber-400 ring-amber-100',
                            'rejected' => 'bg-red-400 ring-red-100',
                            default => 'bg-gray-300 ring-gray-100',
                        };
                    @endphp
                    <div class="w-4 h-4 rounded-full {{ $dotColor }} ring-4"></div>
                </div>
                <div class="flex-1 min-w-0 bg-gray-50/50 rounded-xl px-4 py-3 border border-gray-100/80">
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <p class="text-sm font-semibold text-gray-900">{{ $req->documentType->name }}</p>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $req->status_badge }}">{{ Str::title(str_replace('_', ' ', $req->status)) }}</span>
                    </div>
                    @if($req->purpose)
                        <p class="text-xs text-gray-500 mt-1">{{ $req->purpose }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="text-[11px] text-gray-500">{{ $req->created_at->format('M d, Y · h:i A') }}</span>
                        <span class="text-[10px] text-gray-400">·</span>
                        <span class="text-[10px] text-gray-400">{{ $req->created_at->diffForHumans() }}</span>
                        @if($req->tracking_code)
                            <span class="text-[10px] text-gray-400">·</span>
                            <span class="text-[10px] font-mono text-gray-400">{{ $req->tracking_code }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-10 h-10 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm text-gray-400">No document requests for this resident.</p>
        </div>
        @endif
    </div>
</div>

@endsection
