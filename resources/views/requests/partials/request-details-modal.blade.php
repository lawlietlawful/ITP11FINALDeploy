<div x-show="{{ $modalState }}" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="{{ $modalState }}" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50" @click="{{ $closeAction }}"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div x-show="{{ $modalState }}" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Request #{{ $documentRequest->id }}</h3>
                    <span class="whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $documentRequest->status_badge }}">{{ Str::title(str_replace('_', ' ', $documentRequest->status)) }}</span>
                </div>
                <button @click="{{ $closeAction }}" class="text-gray-400 hover:text-gray-500 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm mb-8">
                    <div>
                        <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Tracking Code</span>
                        <p class="mt-1 font-bold text-primary-700 font-mono">{{ $documentRequest->tracking_code ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Resident</span>
                        <p class="mt-1 font-medium text-gray-900">{{ $documentRequest->resident->full_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Document Type</span>
                        <p class="mt-1 font-medium text-gray-900">{{ $documentRequest->documentType->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Fee</span>
                        <p class="mt-1 text-gray-700 font-medium">PHP {{ number_format($documentRequest->documentType->fee, 2) }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Purpose</span>
                        <p class="mt-1 text-gray-700">{{ $documentRequest->purpose }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Date Filed</span>
                        <p class="mt-1 text-gray-700">{{ $documentRequest->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($documentRequest->processedBy)
                        <div>
                            <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Processed By</span>
                            <p class="mt-1 text-gray-700">{{ $documentRequest->processedBy->name }}</p>
                        </div>
                    @endif
                    @if($documentRequest->released_at)
                        <div>
                            <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Released Date</span>
                            <p class="mt-1 text-gray-700">{{ $documentRequest->released_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif
                    @if($documentRequest->rejection_reason)
                        <div class="sm:col-span-2">
                            <span class="text-gray-400 text-[11px] uppercase tracking-wider font-semibold">Rejection Reason</span>
                            <p class="mt-1 text-red-600 bg-red-50 p-3 rounded-lg">{{ $documentRequest->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                <div class="pt-5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 bg-white">
                    <div class="flex flex-wrap items-center gap-3">
                        @if($documentRequest->status === 'pending')
                            <form action="{{ route('requests.approve', $documentRequest) }}" method="POST">
                                @csrf
                                <button class="btn-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('requests.reject', $documentRequest) }}" method="POST" class="flex gap-2 items-center">
                                @csrf
                                <button class="btn-danger flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reject
                                </button>
                                <input name="rejection_reason" placeholder="Rejection reason..." required class="form-input w-64">
                            </form>
                        @endif
                        @if($documentRequest->status === 'processing')
                            <form action="{{ route('requests.release', $documentRequest) }}" method="POST">
                                @csrf
                                <button class="btn-success">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Mark as Released
                                </button>
                            </form>
                        @endif
                        @if($documentRequest->status === 'released')
                            <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50/80 px-4 py-2.5 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">This document has been released.</span>
                            </div>
                        @endif
                        @if($documentRequest->status === 'rejected')
                            <div class="flex items-center gap-2 text-red-600 bg-red-50/80 px-4 py-2.5 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">This request was rejected.</span>
                            </div>
                        @endif
                    </div>
                    @if(in_array($documentRequest->status, ['processing', 'released']))
                        <a href="{{ route('requests.print', $documentRequest) }}" target="_blank" class="btn-secondary inline-flex items-center gap-2 border-gray-200 hover:border-primary-200 text-gray-700 hover:text-primary-700 font-medium">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Document
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
