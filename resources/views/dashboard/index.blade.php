@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
    <div>
        <p class="text-primary-600 text-sm font-medium mb-1">Welcome back, {{ Auth::user()->name }}!</p>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
    </div>

    {{-- Top Right Actions (Dashboard Only) --}}
    <div class="flex items-center justify-end gap-3 self-end sm:self-auto relative z-50">
        
        {{-- Live Time & Date --}}
        <div class="hidden sm:flex items-center gap-2.5 bg-white/90 border border-gray-200/80 px-4 h-11 rounded-full shadow-sm backdrop-blur-sm transition-all hover:shadow-md"
             x-data="{
                 time: '',
                 date: '',
                 update() {
                     const now = new Date();
                     this.time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                     this.date = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
                 }
             }"
             x-init="update(); setInterval(() => update(), 1000)">
            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-[13px] font-bold text-gray-700 tracking-wide flex items-center gap-1.5">
                <span x-text="date" class="text-gray-500 font-medium"></span>
                <span class="text-gray-300">|</span>
                <span x-text="time" class="text-primary-700"></span>
            </div>
        </div>

        {{-- Notification Dropdown --}}
        <div x-data="{
                open: false,
                unreadCount: {{ $unreadNotificationCount }},
                items: {{ Js::from($notificationItems) }},
                markAllReadUrl: '{{ route('notifications.markAllRead') }}',
                csrfToken: document.querySelector('meta[name=csrf-token]')?.content,
                markingRead: false,
                syncUnreadState() {
                    this.unreadCount = this.items.filter((item) => !item.read_at).length;
                },
                markAllRead() {
                    if (this.markingRead || this.unreadCount === 0) {
                        return;
                    }

                    this.markingRead = true;

                    fetch(this.markAllReadUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        body: JSON.stringify({})
                    })
                    .then((response) => response.ok ? response.json() : Promise.reject(response))
                    .then((payload) => {
                        this.items = this.items.map((item) => ({
                            ...item,
                            read_at: item.read_at ?? payload.read_at,
                        }));
                        this.syncUnreadState();
                    })
                    .catch(() => {})
                    .finally(() => {
                        this.markingRead = false;
                    });
                },
                handleIncoming(notification) {
                    const requestId = notification.request_id ?? notification.data?.request_id ?? null;
                    const item = {
                        id: notification.id,
                        request_id: requestId,
                        resident_name: notification.resident_name ?? notification.data?.resident_name ?? 'Resident',
                        document_name: notification.document_name ?? notification.data?.document_name ?? 'Document',
                        message: notification.message ?? notification.data?.message ?? 'New document request received.',
                        request_url: requestId
                            ? '{{ route('requests.index') }}?open_request=' + requestId
                            : (notification.request_url ?? notification.data?.request_url ?? '{{ route('requests.index') }}'),
                        created_at: notification.created_at_human ?? notification.data?.created_at_human ?? 'Just now',
                        created_at_iso: notification.created_at_iso ?? notification.data?.created_at_iso ?? new Date().toISOString(),
                        read_at: null,
                    };

                    this.items = [item, ...this.items.filter((existing) => existing.id !== item.id)].slice(0, 10);
                    this.syncUnreadState();
                }
            }"
            x-init="
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.private('App.Models.User.{{ Auth::id() }}')
                        .notification((notification) => {
                            handleIncoming(notification);
                        });
                }
            "
            class="relative">
            <button
                @click="
                    open = !open;
                    if (open) {
                        markAllRead();
                    }
                "
                @click.away="open = false"
                class="relative w-11 h-11 flex items-center justify-center text-gray-500 hover:text-primary-600 bg-white/90 hover:bg-white rounded-full transition-all shadow-sm hover:shadow-md border border-gray-200/80 backdrop-blur-sm group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                
                {{-- Fixed Notification Dot with Ping Animation --}}
                <span x-show="unreadCount > 0" x-transition class="absolute top-[8px] right-[10px] flex h-2.5 w-2.5" style="display: none;">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500 border-2 border-white shadow-sm"></span>
                </span>
            </button>
            
            {{-- Dropdown Content --}}
            <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100/80 z-50 overflow-hidden" style="display: none;">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                    <span x-show="unreadCount > 0" class="text-[10px] font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full" x-text="unreadCount + ' New'"></span>
                </div>
                <div class="max-h-[300px] overflow-y-auto">
                    <template x-for="notif in items" :key="notif.id">
                        <a :href="notif.request_url" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50/80 transition-colors" :class="{ 'bg-primary-50/40': !notif.read_at }">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-[13px] font-semibold text-gray-800" x-text="notif.resident_name"></p>
                                <span x-show="!notif.read_at" class="mt-1 inline-flex h-2 w-2 rounded-full bg-primary-500"></span>
                            </div>
                            <p class="text-[12px] text-gray-500 mt-0.5">Requested: <span x-text="notif.document_name"></span></p>
                            <p class="text-[12px] text-gray-500 mt-1 leading-relaxed" x-text="notif.message"></p>
                            <p class="text-[10px] text-gray-400 mt-1.5" x-text="notif.created_at"></p>
                        </a>
                    </template>
                    <div x-show="items.length === 0" class="px-4 py-8 text-center text-sm text-gray-400" style="display: none;">
                        No new notifications.
                    </div>
                </div>
                <div class="p-2 bg-gray-50/50 border-t border-gray-100">
                    <a href="{{ route('requests.index') }}" class="block w-full text-center text-xs font-semibold text-primary-600 hover:text-primary-700 py-1.5">View All Requests</a>
                </div>
            </div>
        </div>

        {{-- Profile Info --}}
        <div class="group flex items-center gap-3 bg-white/90 border border-gray-200/80 pr-4 pl-1.5 h-11 rounded-full shadow-sm hover:shadow-md hover:bg-white cursor-pointer transition-all backdrop-blur-sm relative"
             x-data="{ openProfile: false }" @click.away="openProfile = false">
            <div @click="openProfile = !openProfile" class="flex items-center gap-3 w-full h-full">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white font-extrabold text-[13px] shadow-inner group-hover:scale-105 transition-transform overflow-hidden">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="text-left hidden sm:block">
                    <p class="text-[13px] font-bold text-gray-900 leading-none mb-0.5">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-primary-600 font-bold tracking-wider uppercase leading-none">Administrator</p>
                </div>
            </div>
            
            {{-- Profile Dropdown --}}
            <div x-show="openProfile" x-transition.opacity.duration.200ms class="absolute top-full right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden" style="display: none;">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        My Profile
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Settings
                    </a>
                </div>
                <div class="border-t border-gray-100 py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-rose-600 font-semibold hover:bg-rose-50 hover:text-rose-700 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="space-y-6">

    {{-- Actionable KPIs Row (Fluid Grid) --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 xl:gap-5">
    
    {{-- Card 1: Today's Live Queue --}}
    <div class="glass-card stat-card overview-card group">
        <div class="overview-card-body">
        <div class="min-w-0 flex-1">
            <p class="overview-card-kicker">Today's Live Queue</p>
            <div class="flex items-baseline gap-2">
                <h3 class="overview-card-value">{{ $stats['today_received'] ?? 0 }}</h3>
                <span class="text-sm font-semibold text-gray-400">/ {{ $stats['today_completed'] ?? 0 }} Done</span>
            </div>
            <div class="mt-2 w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                @php 
                    $rcv = $stats['today_received'] ?? 0;
                    $cmp = $stats['today_completed'] ?? 0;
                    $queuePercent = $rcv > 0 ? min(100, ($cmp / $rcv) * 100) : 0; 
                @endphp
                <div class="h-full bg-blue-500 rounded-full transition-all duration-1000" style="width: {{ $queuePercent }}%;"></div>
            </div>
        </div>
        <div class="overview-card-icon overview-card-icon-blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        </div>
    </div>

    {{-- Card 2: Ready for Pickup --}}
    <div class="glass-card stat-card overview-card group">
        <div class="overview-card-body">
        <div class="min-w-0 flex-1">
            <p class="overview-card-kicker">Ready for Pickup</p>
            <h3 class="overview-card-value">{{ $stats['ready_for_pickup'] ?? 0 }}</h3>
            <p class="overview-card-caption text-emerald-600 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Awaiting claim by resident
            </p>
        </div>
        <div class="overview-card-icon overview-card-icon-emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        </div>
    </div>

    {{-- Card 3: Urgent Pending (Bottlenecks) --}}
    <div class="glass-card stat-card overview-card group relative overflow-hidden">
        @if(($stats['urgent_pending'] ?? 0) > 0)
            <div class="absolute inset-0 bg-rose-500/5 animate-pulse pointer-events-none"></div>
        @endif
        <div class="overview-card-body relative z-10">
        <div class="min-w-0 flex-1">
            <p class="overview-card-kicker">Urgent Pending</p>
            <h3 class="overview-card-value {{ ($stats['urgent_pending'] ?? 0) > 0 ? 'text-rose-600' : 'text-gray-900' }}">{{ $stats['urgent_pending'] ?? 0 }}</h3>
            <p class="overview-card-caption text-rose-500 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Delayed > 24 Hours
            </p>
        </div>
        <div class="overview-card-icon {{ ($stats['urgent_pending'] ?? 0) > 0 ? 'overview-card-icon-rose animate-bounce' : 'overview-card-icon-amber' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        </div>
    </div>

    {{-- Card 4: Processing Efficiency --}}
    <div class="glass-card stat-card overview-card group">
        <div class="overview-card-body">
        <div class="min-w-0 flex-1">
            <p class="overview-card-kicker">Avg. Processing Time</p>
            <div class="flex items-baseline gap-1.5">
                <h3 class="overview-card-value">{{ number_format($stats['avg_processing_hours'] ?? 0, 1) }}</h3>
                <span class="text-sm font-bold text-gray-400">Hrs</span>
            </div>
            <p class="overview-card-caption text-violet-600 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Overall turn-around
            </p>
        </div>
        <div class="overview-card-icon overview-card-icon-violet">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        </div>
    </div>
</div>
{{-- Analytics Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Line Chart: Requests Over Time --}}
        <div class="glass-card p-5 lg:col-span-2 flex flex-col">
            <h2 class="text-[15px] font-semibold text-gray-900 mb-4">Requests Over Time (Last 7 Days)</h2>
            <div class="flex-1 relative w-full min-h-[250px]">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        {{-- Doughnut Chart: Document Popularity --}}
        <div class="glass-card p-5 flex flex-col">
            <h2 class="text-[15px] font-semibold text-gray-900 mb-4">Document Popularity</h2>
            <div class="flex-1 relative w-full flex items-center justify-center min-h-[250px]">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Live Activity Feed (2/3 width) --}}
        <div
            x-data="{
                items: {{ Js::from($activityItems) }},
                iconPath(icon) {
                    return {
                        created: 'M12 4v16m8-8H4',
                        released: 'M5 13l4 4L19 7',
                        approved: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        rejected: 'M6 18L18 6M6 6l12 12',
                        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }[icon] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                },
                prependActivity(activity) {
                    this.items = [activity, ...this.items.filter((item) => item.id !== activity.id)].slice(0, 20);
                }
            }"
            x-init="
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.private('admin-dashboard')
                        .listen('.ActivityLogCreated', (activity) => {
                            prependActivity(activity);
                        });
                }
            "
            class="glass-card overflow-hidden lg:col-span-2 flex flex-col h-[380px]">
            <div class="px-6 py-4 border-b border-gray-100/60 flex items-center justify-between bg-white/50 shrink-0 shadow-sm z-10">
                <div class="flex items-center gap-2.5">
                    <h2 class="text-[15px] font-semibold text-gray-900">Live Activity Feed</h2>
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto px-2 py-4 custom-scrollbar">
                <style>
                    .custom-scrollbar::-webkit-scrollbar {
                        width: 4px;
                    }
                    .custom-scrollbar::-webkit-scrollbar-track {
                        background: transparent;
                    }
                    .custom-scrollbar::-webkit-scrollbar-thumb {
                        background-color: #cbd5e1;
                        border-radius: 10px;
                    }
                </style>
                <div class="px-4">
                    <template x-for="activity in items" :key="activity.id">
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-sm z-10" :class="activity.action_badge">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath(activity.action_icon)"></path>
                                </svg>
                            </div>
                            <div class="w-px h-full bg-gray-100 -my-1 z-0"></div>
                        </div>
                        <div class="flex-1 pb-6 pt-1">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-[13px] font-semibold text-gray-900" x-text="activity.title"></h4>
                                    <span x-show="activity.transition_label" class="text-[10px] text-gray-400 font-medium" x-text="activity.transition_label"></span>
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium" x-text="activity.created_at_human"></span>
                            </div>
                            <p class="text-[12px] text-gray-600 leading-relaxed" x-text="activity.description"></p>
                        </div>
                    </div>
                    </template>
                    <div class="text-center py-8">
                        <p x-show="items.length === 0" class="text-sm text-gray-400" style="display: none;">No recent activity.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Panel (1/3 width) --}}
        <div class="glass-card p-5 flex flex-col">
            <h2 class="text-[15px] font-semibold text-gray-900 mb-5">Quick Actions</h2>
            
            <div class="space-y-3 flex-1">
                <a href="{{ route('requests.create') }}" class="group flex items-center p-3 rounded-xl border border-gray-100 hover:border-primary-200 bg-gray-50/50 hover:bg-primary-50/50 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center mr-4 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 group-hover:text-primary-700 transition-colors">New Request</h4>
                        <p class="text-[11px] text-gray-500">Process a walk-in request</p>
                    </div>
                </a>

                <a href="{{ route('residents.create') }}" class="group flex items-center p-3 rounded-xl border border-gray-100 hover:border-violet-200 bg-gray-50/50 hover:bg-violet-50/50 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center mr-4 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 group-hover:text-violet-700 transition-colors">Add Resident</h4>
                        <p class="text-[11px] text-gray-500">Register a new constituent</p>
                    </div>
                </a>

                <a href="{{ route('document-types.create') }}" class="group flex items-center p-3 rounded-xl border border-gray-100 hover:border-amber-200 bg-gray-50/50 hover:bg-amber-50/50 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center mr-4 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 group-hover:text-amber-700 transition-colors">New Doc Type</h4>
                        <p class="text-[11px] text-gray-500">Add a new document template</p>
                    </div>
                </a>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500">System Time</span>
                    <span class="font-semibold text-gray-900">{{ now()->format('h:i A') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Line Chart (Requests Over Time)
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [{
                    label: 'Requests',
                    data: {!! json_encode($chartCounts) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#9ca3af', font: { size: 10, family: 'Inter' } },
                        grid: { color: '#f3f4f6', drawBorder: false }
                    },
                    x: {
                        ticks: { color: '#9ca3af', font: { size: 10, family: 'Inter' } },
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });

        // Doughnut Chart (Document Popularity)
        const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($pieLabels) !!},
                datasets: [{
                    data: {!! json_encode($pieData) !!},
                    backgroundColor: [
                        '#3b82f6', // blue
                        '#8b5cf6', // violet
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#64748b'  // slate
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            color: '#4b5563',
                            font: { family: 'Inter', size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
