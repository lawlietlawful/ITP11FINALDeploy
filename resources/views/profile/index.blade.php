@extends('layouts.app')
@section('content')

<style>
    /* Thin, elegant scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1; /* slate-300 */
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8; /* slate-400 */
    }
</style>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight mb-1.5">Profile Settings</h1>
    <p class="text-sm text-gray-500">Manage your account settings, security, and active sessions.</p>
</div>

{{-- Main Grid: Cards are direct children so they align row-by-row --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8 max-w-7xl pb-12">
    
    {{-- Row 1, Col 1: Profile Information --}}
    <div class="glass-card flex flex-col overflow-hidden">
        <div class="p-6 border-b border-gray-100/80 bg-white/50 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">Profile Information</h3>
            <p class="text-sm text-gray-500 mt-1">Update your account's profile information and email address.</p>
        </div>
        
        @if(session('success') && !str_contains(session('success'), 'Password') && !str_contains(session('success'), 'sessions'))
            <div class="m-6 mb-0 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col flex-1">
            @csrf
            @method('PUT')

            <div class="p-6 flex-1">
                {{-- Photo Upload UI --}}
                <div class="mb-7">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Photo</label>
                    <div class="flex items-center gap-5" x-data="{
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
                        <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white font-extrabold text-2xl shadow-inner overflow-hidden border border-gray-200 ring-4 ring-white flex-shrink-0">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!photoPreview">
                                @if($user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                                @else
                                    <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </template>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="btn-secondary cursor-pointer inline-flex justify-center text-xs py-2 px-4 relative overflow-hidden group border border-gray-300 shadow-sm">
                                <span class="group-hover:text-gray-900 transition-colors font-medium">Select New Photo</span>
                                <input type="file" name="photo" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" @change="updatePreview">
                            </label>
                            @if($user->profile_photo_url)
                                <button type="button" onclick="document.getElementById('remove-photo-form').submit();" class="text-[12px] text-gray-400 font-medium hover:text-rose-500 transition-colors text-left pl-1">
                                    Remove Photo
                                </button>
                            @endif
                        </div>
                    </div>
                    @error('photo') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input w-full">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input w-full">
                        @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex items-center justify-end">
                <button type="submit" class="btn-primary shadow-sm">Save Changes</button>
            </div>
        </form>
        
        @if($user->profile_photo_url)
            <form id="remove-photo-form" action="{{ route('profile.photo.destroy') }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    {{-- Row 1, Col 2: Browser Sessions --}}
    <div class="glass-card flex flex-col overflow-hidden">
        <div class="p-6 border-b border-gray-100/80 bg-white/50 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">Browser Sessions</h3>
            <p class="text-sm text-gray-500 mt-1">Manage and log out your active sessions on other browsers and devices.</p>
        </div>
        
        <div class="p-6 flex-1">
            <p class="text-sm text-gray-600 mb-6 leading-relaxed">If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive.</p>
            
            @if(count($sessions) > 0)
                <div class="space-y-4 mb-8 bg-gray-50/50 border border-gray-100 rounded-xl p-4">
                    @foreach($sessions as $session)
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                @if($session->agent['is_desktop'])
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-900 font-semibold truncate">
                                    {{ $session->agent['platform'] }} - {{ $session->agent['browser'] }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                    {{ $session->ip_address }}, 
                                    @if($session->is_current_device)
                                        <span class="text-emerald-500 font-semibold">This device</span>
                                    @else
                                        Last active {{ $session->last_active }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div x-data="{ confirmingLogout: false }">
                <button @click="confirmingLogout = true" class="btn-secondary font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 shadow-sm w-full sm:w-auto flex justify-center">Log Out Other Browser Sessions</button>

                <template x-teleport="body">
                    <div x-show="confirmingLogout" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="confirmingLogout" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="confirmingLogout = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                            <div x-show="confirmingLogout" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Log Out Other Browser Sessions</h3>
                                    <p class="text-sm text-gray-500 mb-6">Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.</p>
                                    
                                    <form method="POST" action="{{ route('profile.sessions.destroy') }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="mb-6">
                                            <input type="password" name="password" placeholder="Password" required class="form-input w-full" autofocus>
                                            @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="flex justify-end gap-3">
                                            <button type="button" @click="confirmingLogout = false" class="btn-secondary">Cancel</button>
                                            <button type="submit" class="btn-primary bg-rose-600 hover:bg-rose-700 ring-rose-200 text-white border-0">Log Out Sessions</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>


    {{-- Row 2, Col 1: Update Password --}}
    <div class="glass-card flex flex-col overflow-hidden">
        <div class="p-6 border-b border-gray-100/80 bg-white/50 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">Update Password</h3>
            <p class="text-sm text-gray-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        
        @if(session('success') && str_contains(session('success'), 'Password'))
            <div class="m-6 mb-0 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}" class="flex flex-col flex-1">
            @csrf
            @method('PUT')

            <div class="p-6 flex-1 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password</label>
                    <input type="password" name="current_password" required class="form-input w-full">
                    @error('current_password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                    <input type="password" name="password" required class="form-input w-full">
                    @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="form-input w-full">
                </div>
            </div>

            <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex items-center justify-end mt-auto">
                <button type="submit" class="btn-primary shadow-sm bg-gray-800 hover:bg-gray-900 ring-gray-200">Update Password</button>
            </div>
        </form>
    </div>

    {{-- Row 2, Col 2: Recent Activity --}}
    <div class="glass-card flex flex-col overflow-hidden">
        <div class="p-6 border-b border-gray-100/80 bg-white/50 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">Your Recent Activity</h3>
            <p class="text-sm text-gray-500 mt-1">A quick timeline of actions you've recently performed.</p>
        </div>

        <div class="p-6 flex-1 overflow-y-auto custom-scrollbar">
            @if(count($activities) > 0)
                <div class="relative ml-2">
                    <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-gray-100"></div>
                    @foreach($activities as $activity)
                        <div class="relative flex gap-5 pb-6 last:pb-0">
                            <div class="relative z-10 flex-shrink-0 mt-1">
                                <div class="w-3.5 h-3.5 rounded-full bg-primary-500 ring-4 ring-primary-50"></div>
                            </div>
                            <div class="flex-1 min-w-0 bg-gray-50/50 border border-gray-100 rounded-xl p-4 shadow-sm">
                                <p class="text-sm font-bold text-gray-900">{{ $activity->title }}</p>
                                <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $activity->description }}</p>
                                <p class="text-[11px] font-semibold text-gray-400 mt-2.5 uppercase tracking-wider">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">No recent activity</p>
                    <p class="text-xs text-gray-500 mt-1">Actions you perform will appear here.</p>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
