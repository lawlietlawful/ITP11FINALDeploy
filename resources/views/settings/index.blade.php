@extends('layouts.app')
@section('content')

<div class="max-w-7xl mx-auto w-full">
    
    <div class="mb-8 border-b border-gray-200/60 pb-6">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">System Settings</h1>
        <p class="text-base text-gray-500">Configure global application parameters, branding, and security controls.</p>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-12 pb-16">
        @csrf
        @method('PUT')
        <input type="hidden" name="section" value="System">
        <input type="hidden" name="expected_keys[]" value="allow_online_requests">
        <input type="hidden" name="expected_keys[]" value="maintenance_mode">
        
        {{-- Branding & Identity Section --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-1">
                <h3 class="text-lg font-bold text-gray-900">Branding & Identity</h3>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">Configure the core visual identity of your system. This logo and name will be displayed across the admin dashboard and public portal.</p>
            </div>
            
            <div class="xl:col-span-2 glass-card p-6 lg:p-8 flex flex-col gap-8">
                {{-- Logo Upload UI --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">{{ $settings['system_logo']->label ?? 'System Logo' }}</label>
                    <div class="flex items-center gap-6" x-data="{
                        photoPreview: null,
                        updatePreview(event) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { this.photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                            }
                        }
                    }">
                        <div class="relative w-28 h-28 rounded-2xl bg-white flex items-center justify-center overflow-hidden border border-gray-200 shadow-sm flex-shrink-0">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-full h-full object-contain p-2">
                            </template>
                            <template x-if="!photoPreview">
                                @if(isset($settings['system_logo']) && $settings['system_logo']->value)
                                    <img src="{{ $settings['system_logo']->value }}" class="w-full h-full object-contain p-2">
                                @else
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </template>
                        </div>
                        <div class="flex flex-col gap-3">
                            <label class="btn-secondary cursor-pointer inline-flex items-center gap-2 justify-center text-sm py-2 px-5 relative overflow-hidden group border border-gray-300 shadow-sm bg-white hover:bg-gray-50 w-fit">
                                <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span class="group-hover:text-gray-900 transition-colors font-semibold">Upload New Logo</span>
                                <input type="file" name="system_logo" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" @change="updatePreview">
                            </label>
                            <p class="text-xs text-gray-500 max-w-[280px] leading-relaxed">Recommended size: 256x256px. PNG or WebP format with transparent background.</p>
                        </div>
                    </div>
                    @error('system_logo') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ $settings['system_name']->label ?? 'System Name' }}</label>
                    <input type="text" name="system_name" value="{{ $settings['system_name']->value ?? '' }}" class="form-input w-full max-w-lg">
                </div>
            </div>
        </div>

        <div class="h-px bg-gray-200/60 w-full"></div>

        {{-- Contact Information Section --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-1">
                <h3 class="text-lg font-bold text-gray-900">Contact Information</h3>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">Update the official contact details shown to the public on the main portal and email notifications.</p>
            </div>
            
            <div class="xl:col-span-2 glass-card p-6 lg:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ $settings['contact_email']->label ?? 'Contact Email' }}</label>
                        <input type="email" name="contact_email" value="{{ $settings['contact_email']->value ?? '' }}" class="form-input w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ $settings['contact_phone']->label ?? 'Contact Phone' }}</label>
                        <input type="text" name="contact_phone" value="{{ $settings['contact_phone']->value ?? '' }}" class="form-input w-full">
                    </div>
                </div>
            </div>
        </div>

        <div class="h-px bg-gray-200/60 w-full"></div>

        {{-- Social Media Links Section --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-1">
                <h3 class="text-lg font-bold text-gray-900">Social Media</h3>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">Connect your public portal to your official social media accounts. Leave blank to hide the icons.</p>
            </div>
            
            <div class="xl:col-span-2 glass-card p-6 lg:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"></path></svg>
                            {{ $settings['facebook_url']->label ?? 'Facebook URL' }}
                        </label>
                        <input type="url" name="facebook_url" value="{{ $settings['facebook_url']->value ?? '' }}" class="form-input w-full" placeholder="https://facebook.com/...">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-1.5">
                            <svg class="w-4 h-4 text-sky-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                            {{ $settings['twitter_url']->label ?? 'Twitter URL' }}
                        </label>
                        <input type="url" name="twitter_url" value="{{ $settings['twitter_url']->value ?? '' }}" class="form-input w-full" placeholder="https://twitter.com/...">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-1.5">
                            <svg class="w-4 h-4 text-pink-600" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            {{ $settings['instagram_url']->label ?? 'Instagram URL' }}
                        </label>
                        <input type="url" name="instagram_url" value="{{ $settings['instagram_url']->value ?? '' }}" class="form-input w-full max-w-md" placeholder="https://instagram.com/...">
                    </div>
                </div>
            </div>
        </div>

        <div class="h-px bg-gray-200/60 w-full"></div>

        {{-- System Controls Section --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-1">
                <h3 class="text-lg font-bold text-gray-900">System Controls</h3>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">Manage global access rules and maintenance status. Use these to temporarily disable features during updates.</p>
            </div>
            
            <div class="xl:col-span-2 glass-card p-6 lg:p-8">
                <div class="flex flex-col gap-6">
                    <label class="flex items-start gap-4 cursor-pointer group bg-gray-50/50 border border-gray-200/60 p-5 rounded-2xl hover:bg-white transition-colors shadow-sm">
                        <div class="relative flex-shrink-0 mt-0.5">
                            <input type="checkbox" name="allow_online_requests" value="true" class="sr-only peer" {{ ($settings['allow_online_requests']->value ?? 'false') === 'true' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-500"></div>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-gray-900 mb-1">{{ $settings['allow_online_requests']->label ?? 'Allow Online Requests' }}</span>
                            <span class="block text-[13px] text-gray-500 leading-relaxed">Toggle whether residents can submit new document requests via the public portal.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-4 cursor-pointer group bg-rose-50/30 border border-rose-100/50 p-5 rounded-2xl hover:bg-rose-50/60 transition-colors shadow-sm">
                        <div class="relative flex-shrink-0 mt-0.5">
                            <input type="checkbox" name="maintenance_mode" value="true" class="sr-only peer" {{ ($settings['maintenance_mode']->value ?? 'false') === 'true' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-500"></div>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-rose-900 mb-1">{{ $settings['maintenance_mode']->label ?? 'Maintenance Mode' }}</span>
                            <span class="block text-[13px] text-rose-700/70 leading-relaxed">Enable maintenance mode to restrict all public access to the system.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Footer Save Button --}}
        <div class="mt-8 flex justify-end">
            <button type="submit" class="btn-primary shadow-lg shadow-primary-500/30 px-8 py-3 text-base font-bold rounded-xl hover:-translate-y-0.5 transition-all">
                Save All Settings
            </button>
        </div>

    </form>
</div>

@endsection
