<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\Resident;
use App\Models\DocumentType;

class DashboardController extends Controller {

    public function index() {
        $stats = [
            'total_requests'    => DocumentRequest::count(),
            'pending'           => DocumentRequest::where('status', 'pending')->count(),
            'processing'        => DocumentRequest::where('status', 'processing')->count(),
            'released'          => DocumentRequest::where('status', 'released')->count(),
            'rejected'          => DocumentRequest::where('status', 'rejected')->count(),
            'total_residents'   => Resident::count(),
            'total_doc_types'   => DocumentType::where('is_active', true)->count(),
            'total_revenue'     => DocumentRequest::where('status', 'released')
                                    ->join('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
                                    ->sum('document_types.fee'),
            'monthly_revenue'   => DocumentRequest::where('status', 'released')
                                    ->whereMonth('document_requests.created_at', now()->month)
                                    ->whereYear('document_requests.created_at', now()->year)
                                    ->join('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
                                    ->sum('document_types.fee'),
        ];

        $recent = DocumentRequest::with(['resident', 'documentType'])
            ->latest()
            ->take(5)
            ->get();

        // 1. Line Chart Data (Last 7 Days)
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $requestsLast7Days = DocumentRequest::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })
            ->map(function($group) {
                return $group->count();
            });

        $chartDates = $dates->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray();
        $chartCounts = $dates->map(fn($d) => $requestsLast7Days->get($d, 0))->toArray();

        // 2. Doughnut Chart Data (By Document Type)
        $docTypesDist = DocumentRequest::with('documentType')
            ->get()
            ->groupBy('document_type_id')
            ->map(function($group) {
                return [
                    'name' => $group->first()->documentType->name ?? 'Unknown',
                    'count' => $group->count()
                ];
            })->values();

        $pieLabels = $docTypesDist->pluck('name')->toArray();
        $pieData = $docTypesDist->pluck('count')->toArray();

        // 3. Notifications (Top 5 Pending)
        $notifications = DocumentRequest::with(['resident', 'documentType'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'recent', 'chartDates', 'chartCounts', 'pieLabels', 'pieData', 'notifications'
        ));
    }
}
