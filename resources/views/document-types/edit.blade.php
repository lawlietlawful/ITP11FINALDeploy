@extends('layouts.app')
@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('document-types.index') }}" class="text-sm text-gray-400 hover:text-primary-600 flex items-center gap-1 mb-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Document Types
        </a>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Document Type</h1>
    </div>

    <div class="glass-card p-6 lg:p-8">
        <form method="POST" action="{{ route('document-types.update', $documentType) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-600 mb-1.5">Document Name <span class="text-red-400">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $documentType->name) }}" required
                       class="form-input w-full">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-600 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="form-input w-full resize-none">{{ old('description', $documentType->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="fee" class="block text-sm font-medium text-gray-600 mb-1.5">Fee (₱) <span class="text-red-400">*</span></label>
                    <input type="number" id="fee" name="fee" value="{{ old('fee', $documentType->fee) }}" step="0.01" min="0" required
                           class="form-input w-full">
                </div>
                <div>
                    <label for="processing_days" class="block text-sm font-medium text-gray-600 mb-1.5">Processing Days <span class="text-red-400">*</span></label>
                    <input type="number" id="processing_days" name="processing_days" value="{{ old('processing_days', $documentType->processing_days) }}" min="1" required
                           class="form-input w-full">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $documentType->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <label for="is_active" class="text-sm font-medium text-gray-600">Active</label>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="btn-primary">Update Document Type</button>
                <a href="{{ route('document-types.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
