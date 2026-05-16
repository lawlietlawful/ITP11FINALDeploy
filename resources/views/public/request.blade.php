@extends('layouts.public')

@section('title', 'Submit Document Request | VistáBarangay')

@section('content')
    {{-- Main Content Container --}}
    <div class="flex-1 max-w-4xl w-full mx-auto px-6 py-10">
        {{-- Elegant Multi-Step Indicator --}}
        <div class="max-w-xl mx-auto flex items-center justify-center mb-10">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center text-xs font-bold shadow-md shadow-brand-500/20">1</span>
                <span class="text-xs font-bold text-slate-900">Application Details</span>
            </div>
            <div class="flex-1 h-0.5 bg-slate-200 mx-3.5"></div>
            <div class="flex items-center gap-2.5 opacity-50">
                <span class="w-8 h-8 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">2</span>
                <span class="text-xs font-bold text-slate-500 hidden sm:inline">Verification</span>
            </div>
            <div class="flex-1 h-0.5 bg-slate-200 mx-3.5 hidden sm:block"></div>
            <div class="hidden sm:flex items-center gap-2.5 opacity-50">
                <span class="w-8 h-8 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold">3</span>
                <span class="text-xs font-bold text-slate-500">Tracking Issued</span>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-xl shadow-slate-100 overflow-hidden">
            {{-- Header Title area --}}
            <div class="px-8 pt-8 pb-6 bg-gradient-to-b from-slate-50/50 to-white border-b border-slate-100">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Request an Official Document</h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">Please provide accurate identification details. Required fields are marked with an asterisk (*).</p>
            </div>

            {{-- Form Errors Banner --}}
            @if($errors->any())
                <x-alert type="error" title="Validation Requirements" class="mx-8 mt-6 shadow-none backdrop-blur-0">
                    <ul class="space-y-1.5 text-xs sm:text-sm">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <span class="mt-[6px] h-1.5 w-1.5 flex-shrink-0 rounded-full bg-current opacity-70"></span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <form method="POST" action="{{ route('public.submit') }}" class="p-8 space-y-8" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf

                {{-- Honeypot Anti-Bot Trap (hidden from humans, filled by bots) --}}
                <div style="display:none !important" aria-hidden="true">
                    <label for="website">Leave this empty</label>
                    <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off">
                </div>

                {{-- Section: Personal Details --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                        <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-bold">👤</span>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Personal Identification</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="first_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">First Name <span class="text-rose-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800 placeholder:text-slate-400"
                                   placeholder="e.g. Juan">
                        </div>
                        <div>
                            <label for="middle_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800 placeholder:text-slate-400"
                                   placeholder="e.g. Santos">
                        </div>
                        <div>
                            <label for="last_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Last Name <span class="text-rose-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800 placeholder:text-slate-400"
                                   placeholder="e.g. Dela Cruz">
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Residential Address (Purok) <span class="text-rose-500">*</span></label>
                        <select id="address" name="address" required
                                class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800 bg-white cursor-pointer">
                            <option value="" disabled {{ old('address') ? '' : 'selected' }}>— Select Purok —</option>
                            @foreach(['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5', 'Purok 6'] as $purok)
                                <option value="{{ $purok }}" {{ old('address') === $purok ? 'selected' : '' }}>{{ $purok }}</option>
                            @endforeach
                        </select>
                        <span class="block text-[11px] text-slate-400 font-medium mt-1">Select your official designated Purok jurisdiction.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="contact_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800"
                                   placeholder="09171234567">
                            <span class="block text-[11px] text-slate-400 font-medium mt-1">11 digits starting with 09</span>
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800"
                                   placeholder="juan@example.com">
                            <span class="block text-[11px] text-slate-400 font-medium mt-1">For Ready-to-Pickup notifications</span>
                        </div>
                        <div>
                            <label for="birthdate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Birthdate <span class="text-rose-500">*</span></label>
                            <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" required
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800"
                                   max="{{ now()->subYears(18)->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="gender" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Gender</label>
                            <select id="gender" name="gender"
                                    class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800">
                                <option value="">— Select —</option>
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="civil_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Civil Status</label>
                            <select id="civil_status" name="civil_status"
                                    class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800">
                                <option value="">— Select —</option>
                                <option value="Single" {{ old('civil_status') === 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') === 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Widowed" {{ old('civil_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Separated" {{ old('civil_status') === 'Separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section: Request Intent --}}
                <div class="space-y-5 pt-3">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                        <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-bold">📄</span>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Document Intent & Fees</h3>
                    </div>

                    <div>
                        <label for="document_type_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Requested Document Type <span class="text-rose-500">*</span></label>
                        <select id="document_type_id" name="document_type_id" required
                                class="input-premium w-full px-4 py-3 text-sm font-bold text-slate-800 bg-white border-slate-200 shadow-sm cursor-pointer">
                            <option value="" disabled {{ old('document_type_id', request('doc_type')) ? '' : 'selected' }}>Select from official catalog...</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ old('document_type_id', request('doc_type')) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} — Fee: ₱{{ number_format($type->fee, 2) }} (Processing: {{ $type->processing_days }} {{ Str::plural('day', $type->processing_days) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="purpose" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Stated Purpose for Request <span class="text-rose-500">*</span></label>
                        <textarea id="purpose" name="purpose" rows="3" required
                                  class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800 placeholder:text-slate-400 resize-none"
                                  placeholder="Provide definitive explanation (e.g., Mandatory supporting submission for localized scholarship application, local work authentication...)"></textarea>
                        <script>document.getElementById('purpose').value = @json(old('purpose', ''));</script>
                        <span class="block text-[11px] text-slate-400 font-medium mt-1">Official records strictly audit requested justification prior to authorization approval.</span>
                    </div>
                </div>


                {{-- Action Panel --}}
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('public.home') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-xs text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors text-center uppercase tracking-wider">
                        Cancel Application
                    </a>
                    <button type="submit" x-bind:disabled="isSubmitting" :class="{ 'opacity-70 cursor-not-allowed': isSubmitting }" class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 shadow-xl shadow-brand-500/20 hover:shadow-brand-500/30 transition-all uppercase tracking-wider inline-flex items-center justify-center gap-2">
                        <span x-text="isSubmitting ? 'Submitting...' : 'Authenticate & Submit'">Authenticate & Submit</span>
                        <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <svg x-show="isSubmitting" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
