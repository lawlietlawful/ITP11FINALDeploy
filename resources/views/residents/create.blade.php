@extends('layouts.app')
@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('residents.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1 mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Residents
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Add New Resident</h1>
    </div>

    {{-- Duplicate Warning --}}
    @if(session('warning'))
        <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('warning') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-card p-6 lg:p-8">
        <form method="POST" action="{{ route('residents.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-600 mb-1.5">First Name <span class="text-red-400">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                           class="form-input w-full">
                </div>
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-600 mb-1.5">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                           class="form-input w-full">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-600 mb-1.5">Last Name <span class="text-red-400">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                           class="form-input w-full">
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-600 mb-1.5">Address <span class="text-red-400">*</span></label>
                <textarea id="address" name="address" rows="3" required
                          class="form-input w-full resize-none">{{ old('address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-600 mb-1.5">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                           class="form-input w-full"
                           placeholder="09171234567">
                    <p class="text-xs text-gray-400 mt-1">Philippine mobile format (11 digits starting with 09)</p>
                </div>
                <div>
                    <label for="birthdate" class="block text-sm font-medium text-gray-600 mb-1.5">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}"
                           class="form-input w-full" max="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-600 mb-1.5">Gender</label>
                    <select id="gender" name="gender" class="form-input w-full">
                        <option value="">— Select —</option>
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label for="civil_status" class="block text-sm font-medium text-gray-600 mb-1.5">Civil Status</label>
                    <select id="civil_status" name="civil_status" class="form-input w-full">
                        <option value="">— Select —</option>
                        <option value="Single" {{ old('civil_status') === 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('civil_status') === 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Widowed" {{ old('civil_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Separated" {{ old('civil_status') === 'Separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="btn-primary">Save Resident</button>
                <a href="{{ route('residents.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
