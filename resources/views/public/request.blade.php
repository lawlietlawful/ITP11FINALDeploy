<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Document Request | VistáBarangay</title>
    <meta name="description" content="Secure online application form for requesting official barangay documents, certificates, and clearances.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cd0fd',
                            400: '#36bffa',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .input-premium {
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-premium:focus {
            background: #ffffff;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
            outline: none;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col">

    {{-- Premium Header Navbar --}}
    <header class="sticky top-0 z-50 glass-nav">
        <div class="max-w-4xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('New Logo.png') }}" alt="VistáBarangay Logo" class="w-9 h-9 flex-shrink-0 rounded-full object-cover shadow-sm ring-2 ring-brand-100 group-hover:scale-105 transition-transform duration-200">
                <span class="font-bold text-base text-slate-900 tracking-tight">Vistá<span class="text-brand-600">Barangay</span></span>
            </a>
            
            <a href="{{ route('public.track') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 px-3.5 py-2 rounded-lg transition-colors border border-brand-100/50">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Track Request
            </a>
        </div>
    </header>

    {{-- Main Content Container --}}
    <main class="flex-1 max-w-4xl w-full mx-auto px-6 py-10">
        
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
                <div class="mx-8 mt-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 flex gap-3 items-start">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider block mb-1">Validation Requirements</span>
                        <ul class="list-disc list-inside text-xs space-y-1 font-medium text-rose-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('public.submit') }}" class="p-8 space-y-8">
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="contact_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800 placeholder:text-slate-400"
                                   placeholder="09171234567">
                            <span class="block text-[11px] text-slate-400 font-medium mt-1">PH mobile format (11 digits starting with 09)</span>
                        </div>
                        <div>
                            <label for="birthdate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Birthdate</label>
                            <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}"
                                   class="input-premium w-full px-4 py-3 text-sm font-medium text-slate-800"
                                   max="{{ date('Y-m-d') }}">
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

                {{-- Agreement Notice --}}
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-500 leading-relaxed font-normal">
                    <span class="font-bold text-slate-700">Security Notification:</span> Submitting requests logs network access footprints including timestamp coordinates and address originators to prevent platform enumeration abuse. Ensure credentials comply with local standard policies.
                </div>

                {{-- Action Panel --}}
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('public.home') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-xs text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors text-center uppercase tracking-wider">
                        Cancel Application
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 shadow-xl shadow-brand-500/20 hover:shadow-brand-500/30 transition-all uppercase tracking-wider inline-flex items-center justify-center gap-2">
                        <span>Authenticate & Submit</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>

    </main>

    {{-- Premium Footer Compact --}}
    <footer class="bg-slate-900 text-slate-500 py-6 border-t border-slate-800 mt-12 text-xs text-center">
        <div class="max-w-4xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} VistáBarangay. Secure Submissions.</p>
            <div class="flex gap-4 text-slate-600 font-medium">
                <a href="{{ route('login') }}" class="hover:text-slate-400 transition-colors">Admin Hub</a>
            </div>
        </div>
    </footer>

</body>
</html>
