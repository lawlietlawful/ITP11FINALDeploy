@extends('layouts.app')
@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('residents.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1 mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Residents
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Resident</h1>
    </div>

    <div class="glass-card p-6 lg:p-8">
        <form method="POST" action="{{ route('residents.update', $resident) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-600 mb-1.5">First Name <span class="text-red-400">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $resident->first_name) }}" required
                           class="form-input w-full">
                </div>
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-600 mb-1.5">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $resident->middle_name) }}"
                           class="form-input w-full">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-600 mb-1.5">Last Name <span class="text-red-400">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $resident->last_name) }}" required
                           class="form-input w-full">
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-600 mb-1.5">Address (Purok) <span class="text-red-400">*</span></label>
                <select id="address" name="address" required class="form-input w-full cursor-pointer">
                    <option value="" disabled {{ old('address', $resident->address) ? '' : 'selected' }}>— Select Purok —</option>
                    @foreach(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5', 'Purok 6'] as $purok)
                        <option value="{{ $purok }}" {{ old('address', $resident->address) === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-600 mb-1.5">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $resident->contact_number) }}"
                           class="form-input w-full" placeholder="09171234567">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-600 mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $resident->email) }}"
                           class="form-input w-full" placeholder="juan@example.com">
                </div>
                <div>
                    <label for="birthdate" class="block text-sm font-medium text-gray-600 mb-1.5">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate', $resident->birthdate?->format('Y-m-d')) }}"
                           class="form-input w-full" max="{{ now()->subYears(18)->format('Y-m-d') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-600 mb-1.5">Gender</label>
                    <select id="gender" name="gender" class="form-input w-full">
                        <option value="">— Select —</option>
                        <option value="Male" {{ old('gender', $resident->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $resident->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label for="civil_status" class="block text-sm font-medium text-gray-600 mb-1.5">Civil Status</label>
                    <select id="civil_status" name="civil_status" class="form-input w-full">
                        <option value="">— Select —</option>
                        <option value="Single" {{ old('civil_status', $resident->civil_status) === 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('civil_status', $resident->civil_status) === 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Widowed" {{ old('civil_status', $resident->civil_status) === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Separated" {{ old('civil_status', $resident->civil_status) === 'Separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="btn-primary">Update Resident</button>
                <a href="{{ route('residents.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
