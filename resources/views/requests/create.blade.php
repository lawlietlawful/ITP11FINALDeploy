@extends('layouts.app')
@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('requests.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1 mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Requests
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">New Document Request</h1>
    </div>

    <div class="glass-card p-6 lg:p-8">
        <form method="POST" action="{{ route('requests.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="resident_id" class="block text-sm font-medium text-gray-600 mb-1.5">Resident <span class="text-red-400">*</span></label>
                <select id="resident_id" name="resident_id" required
                        class="form-input w-full">
                    <option value="">Select a resident...</option>
                    @foreach($residents as $resident)
                        <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                            {{ $resident->last_name }}, {{ $resident->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="document_type_id" class="block text-sm font-medium text-gray-600 mb-1.5">Document Type <span class="text-red-400">*</span></label>
                <select id="document_type_id" name="document_type_id" required
                        class="form-input w-full">
                    <option value="">Select document type...</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} (₱{{ number_format($type->fee, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="purpose" class="block text-sm font-medium text-gray-600 mb-1.5">Purpose <span class="text-red-400">*</span></label>
                <textarea id="purpose" name="purpose" rows="3" required
                          class="form-input w-full resize-none"
                          placeholder="State the purpose of this document request...">{{ old('purpose') }}</textarea>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="btn-primary">Submit Request</button>
                <a href="{{ route('requests.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
