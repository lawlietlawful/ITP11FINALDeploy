@extends('layouts.app')
@section('content')

{{-- Page Header --}}
<div class="mb-7 flex justify-between items-start">
    <div>
        <p class="text-primary-600 text-sm font-medium mb-1">Welcome back, {{ Auth::user()->name }}!</p>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
    </div>

    {{-- Top Right Actions (Dashboard Only) --}}
    <div class="flex items-center gap-3">
        
        {{-- Notification Dropdown --}}
        <div x-data="{
                open: false,
                unreadCount: {{ $notifications->count() }},
                items: {{ json_encode($notifications->map(fn($n) => [
                    'id' => $n->id,
                    'resident_name' => $n->resident->full_name,
                    'document_name' => $n->documentType->name,
                    'created_at' => $n->created_at->diffForHumans()
                ])) }}
            }"
            x-init="
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.private('admin-notifications')
                        .listen('.DocumentRequestCreated', (e) => {
                            items.unshift(e);
                            unreadCount++;
                        });
                }
            "
            class="relative">
            <button @click="open = !open" @click.away="open = false" class="relative w-10 h-10 flex items-center justify-center text-gray-500 hover:text-primary-600 bg-white hover:bg-gray-50 rounded-full transition-all shadow-sm border border-gray-100/80 backdrop-blur-sm">
                <svg class="w-[20px] h-[20px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span x-show="unreadCount > 0" x-transition class="absolute top-2 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse" style="display: none;"></span>
            </button>
            
            {{-- Dropdown Content --}}
            <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100/80 z-50 overflow-hidden" style="display: none;">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
                    <span x-show="unreadCount > 0" class="text-[10px] font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full" x-text="unreadCount + ' New'"></span>
                </div>
                <div class="max-h-[300px] overflow-y-auto">
                    <template x-for="notif in items" :key="notif.id">
                        <a href="{{ route('requests.index') }}" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50/80 transition-colors">
                            <p class="text-[13px] font-semibold text-gray-800" x-text="notif.resident_name"></p>
                            <p class="text-[12px] text-gray-500 mt-0.5">Requested: <span x-text="notif.document_name"></span></p>
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
        <div class="flex items-center gap-2.5 bg-white/80 border border-gray-100/80 pr-4 pl-1 h-10 rounded-full shadow-sm backdrop-blur-sm">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-xs shadow-inner">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="text-left hidden sm:block">
                <p class="text-[12px] font-bold text-gray-800 leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-gray-500 font-bold tracking-wider uppercase mt-0.5">Administrator</p>
            </div>
        </div>
    </div>
</div>

<div class="space-y-6">

    {{-- Top Features Row (4 compact cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-violet-50 flex items-center justify-center text-violet-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Total Residents</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_residents'] }}</h3>
            </div>
        </div>

        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Total Requests</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_requests'] }}</h3>
            </div>
        </div>

        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Pending Action</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</h3>
            </div>
        </div>

        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Monthly Revenue</p>
                <h3 class="text-xl font-bold text-gray-900">₱{{ number_format($stats['monthly_revenue'], 2) }}</h3>
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
        
        {{-- Recent Requests Table (2/3 width) --}}
        <div class="glass-card overflow-hidden lg:col-span-2">
            <div class="px-6 py-4 border-b border-gray-100/60 flex items-center justify-between bg-white/50">
                <h2 class="text-[15px] font-semibold text-gray-900">Recent Requests</h2>
                <a href="{{ route('requests.index') }}" class="text-[12px] font-semibold text-primary-600 hover:text-primary-700 uppercase tracking-wider transition-colors bg-primary-50 px-3 py-1.5 rounded-lg">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/40 border-b border-gray-100/60">
                            <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Resident</th>
                            <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Document</th>
                            <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50/80">
                        @forelse($recent as $req)
                        <tr class="table-row hover:bg-gray-50/30 transition-colors">
                            <td class="px-6 py-3.5 font-medium text-gray-900">{{ $req->resident->full_name }}</td>
                            <td class="px-6 py-3.5 text-gray-600">{{ $req->documentType->name }}</td>
                            <td class="px-6 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $req->status_badge }}">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-gray-400">{{ $req->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            No requests yet.
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
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
