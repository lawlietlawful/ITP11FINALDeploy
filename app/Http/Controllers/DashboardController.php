<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Notifications\NewDocumentRequestNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {

    public function index() {
        $user = Auth::user();
        $driver = DB::getDriverName();

        $avgProcessingHours = $driver === 'sqlite'
            ? (DocumentRequest::where('status', 'released')
                ->whereNotNull('released_at')
                ->get()
                ->avg(fn (DocumentRequest $request) => $request->created_at->diffInHours($request->released_at)))
            : DocumentRequest::where('status', 'released')
                ->whereNotNull('released_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, released_at)) as avg_hours')
                ->value('avg_hours');

        $stats = [
            // Card 1: Today's Live Queue
            'today_received'    => DocumentRequest::whereDate('created_at', today())->count(),
            'today_completed'   => DocumentRequest::whereDate('released_at', today())->where('status', 'released')->count(),
            
            // Card 2: Ready for Pickup
            'ready_for_pickup'  => DocumentRequest::where('status', 'released')->where('released_at', '>=', now()->subDays(30))->count(),
            
            // Card 3: Urgent Pending (older than 24 hours)
            'urgent_pending'    => DocumentRequest::whereIn('status', ['pending', 'processing'])
                                    ->where('created_at', '<', now()->subHours(24))
                                    ->count(),
            
            // Card 4: Processing Efficiency
            'avg_processing_hours' => $avgProcessingHours ?? 0,
        ];

        $activities = \App\Models\ActivityLog::with(['user', 'subject'])
            ->latest()
            ->take(20)
            ->get();

        $activityItems = $activities->map(
            fn (\App\Models\ActivityLog $activity) => $activity->toDashboardPayload()
        )->values();

        // 1. Line Chart Data (Last 7 Days)
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $requestsLast7Days = DocumentRequest::query()
            ->selectRaw('DATE(created_at) as request_date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('request_date')
            ->pluck('total', 'request_date');

        $chartDates = $dates->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray();
        $chartCounts = $dates->map(fn($d) => $requestsLast7Days->get($d, 0))->toArray();

        // 2. Doughnut Chart Data (By Document Type)
        $docTypesDist = DocumentRequest::query()
            ->leftJoin('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
            ->selectRaw("COALESCE(document_types.name, 'Unknown') as name, COUNT(document_requests.id) as total")
            ->groupBy('document_requests.document_type_id', 'document_types.name')
            ->orderByDesc('total')
            ->get();

        $pieLabels = $docTypesDist->pluck('name')->toArray();
        $pieData = $docTypesDist->pluck('total')->toArray();

        $notifications = $user->notifications()
            ->where('type', NewDocumentRequestNotification::class)
            ->latest()
            ->take(10)
            ->get();

        $notificationItems = $notifications->map(function ($notification) {
            $data = $notification->data;

            return [
                'id' => $notification->id,
                'request_id' => $data['request_id'] ?? null,
                'resident_name' => $data['resident_name'] ?? 'Resident',
                'document_name' => $data['document_name'] ?? 'Document',
                'message' => $data['message'] ?? 'New document request received.',
                'request_url' => isset($data['request_id'])
                    ? route('requests.index', ['open_request' => $data['request_id']])
                    : ($data['request_url'] ?? route('requests.index')),
                'created_at' => $data['created_at_human'] ?? $notification->created_at->diffForHumans(),
                'created_at_iso' => $data['created_at_iso'] ?? $notification->created_at->toIso8601String(),
                'read_at' => $notification->read_at?->toIso8601String(),
            ];
        })->values();

        $unreadNotificationCount = $user->unreadNotifications()
            ->where('type', NewDocumentRequestNotification::class)
            ->count();

        return view('dashboard.index', compact(
            'stats',
            'activities',
            'activityItems',
            'chartDates',
            'chartCounts',
            'pieLabels',
            'pieData',
            'notificationItems',
            'unreadNotificationCount'
        ));
    }
}
