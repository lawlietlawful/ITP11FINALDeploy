@extends('layouts.app')
@section('content')
<div class="mb-7">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">System Settings</h1>
    <p class="text-sm text-gray-400 mt-1">Configure global application parameters.</p>
</div>

<div class="glass-card p-6 max-w-4xl">
    @if(session('success'))
        <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            {{-- General Info --}}
            <div>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">General Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">{{ $settings['system_name']->label }}</label>
                        <input type="text" name="system_name" value="{{ $settings['system_name']->value }}" class="form-input w-full">
                    </div>
                </div>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">{{ $settings['contact_email']->label }}</label>
                        <input type="email" name="contact_email" value="{{ $settings['contact_email']->value }}" class="form-input w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">{{ $settings['contact_phone']->label }}</label>
                        <input type="text" name="contact_phone" value="{{ $settings['contact_phone']->value }}" class="form-input w-full">
                    </div>
                </div>
            </div>

            {{-- Toggles --}}
            <div>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">System Controls</h3>
                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="allow_online_requests" value="true" class="sr-only peer" {{ $settings['allow_online_requests']->value === 'true' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-500"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">{{ $settings['allow_online_requests']->label }}</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="maintenance_mode" value="true" class="sr-only peer" {{ $settings['maintenance_mode']->value === 'true' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-500"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">{{ $settings['maintenance_mode']->label }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
            <button type="submit" class="btn-primary">Save Settings</button>
        </div>
    </form>
</div>
@endsection
