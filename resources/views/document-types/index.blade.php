@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between mb-6" x-data="{ createModal: {{ $errors->any() ? 'true' : 'false' }} }">
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

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Document Name <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="form-input w-full" placeholder="e.g. Barangay Clearance">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Description</label>
                                <textarea name="description" rows="3" class="form-input w-full resize-none" placeholder="Brief description of the document...">{{ old('description') }}</textarea>
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

<div class="glass-card overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50/40">
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Fee</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Processing Days</th>
                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50/80">
            @forelse($types as $type)
            <tr class="table-row" x-data="{ editModal: false, deleteModal: false }">
                <td class="px-6 py-3.5 font-medium text-gray-900">{{ $type->name }}</td>
                <td class="px-6 py-3.5 text-gray-600 max-w-xs truncate">{{ $type->description ?? '—' }}</td>
                <td class="px-6 py-3.5 text-gray-700 font-medium">₱{{ number_format($type->fee, 2) }}</td>
                <td class="px-6 py-3.5 text-gray-600">{{ $type->processing_days }} {{ Str::plural('day', $type->processing_days) }}</td>
                <td class="px-6 py-3.5">
                    @if($type->is_active)
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-green-100/80 text-green-800">Active</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100/80 text-gray-600">Inactive</span>
                    @endif
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
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Document Name <span class="text-red-400">*</span></label>
                                                <input type="text" name="name" value="{{ $type->name }}" required class="form-input w-full">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-600 mb-1.5">Description</label>
                                                <textarea name="description" rows="3" class="form-input w-full resize-none">{{ $type->description }}</textarea>
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
            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No document types found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $types->links() }}</div>

@endsection
