<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\Resident;
use App\Models\DocumentType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller {

    public function index(Request $request) {
        $requests = DocumentRequest::with(['resident', 'documentType'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) =>
                $q->whereHas('resident', fn($r) =>
                    $r->where('first_name', 'like', "%{$request->search}%")
                      ->orWhere('last_name', 'like', "%{$request->search}%")
                )
            )
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $residents     = Resident::orderBy('last_name')->get();
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        $stats = [
            'pending'        => DocumentRequest::where('status', 'pending')->count(),
            'processing'     => DocumentRequest::where('status', 'processing')->count(),
            'released_month' => DocumentRequest::where('status', 'released')
                                    ->where('released_at', '>=', now()->startOfMonth())
                                    ->count(),
            'total'          => DocumentRequest::count(),
        ];

        return view('requests.index', compact('requests', 'residents', 'documentTypes', 'stats'));
    }

    public function create() {
        $residents     = Resident::orderBy('last_name')->get();
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        return view('requests.create', compact('residents', 'documentTypes'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'resident_id'      => 'required|exists:residents,id',
            'document_type_id' => 'required|exists:document_types,id',
            'purpose'          => 'required|string|max:500',
        ]);

        // ─── Business Rule: Prevent duplicate requests ───
        $duplicateExists = DocumentRequest::where('resident_id', $validated['resident_id'])
            ->where('document_type_id', $validated['document_type_id'])
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($duplicateExists) {
            return back()->withInput()->withErrors([
                'document_type_id' => 'This resident already has a pending or processing request for this document type.',
            ]);
        }

        $docRequest = DocumentRequest::create([
            'tracking_code'    => DocumentRequest::generateTrackingCode(),
            'resident_id'      => $validated['resident_id'],
            'document_type_id' => $validated['document_type_id'],
            'purpose'          => $validated['purpose'],
            'status'           => 'pending',
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
        ]);

        // Audit log
        ActivityLog::record(
            action: 'created',
            subject: $docRequest,
            newStatus: 'pending',
            description: "Admin-created request for resident #{$validated['resident_id']}",
        );

        try {
            \App\Events\DocumentRequestCreated::dispatch($docRequest);
        } catch (\Throwable $e) {
            // Broadcasting is non-critical — don't block the request if Reverb is offline
        }

        return redirect()->route('requests.index')->with('success', 'Request created successfully.');
    }

    public function show(DocumentRequest $request_item) {
        $request_item->load(['resident', 'documentType', 'processedBy']);

        // Fetch audit logs for this request
        $auditLogs = ActivityLog::where('subject_type', DocumentRequest::class)
            ->where('subject_id', $request_item->id)
            ->with('user')
            ->latest()
            ->get();

        return view('requests.show', compact('request_item', 'auditLogs'));
    }

    // Approve → processing (admin & staff)
    public function approve(DocumentRequest $request_item) {
        $oldStatus = $request_item->status;

        $request_item->update([
            'status'       => 'processing',
            'processed_by' => Auth::id(),
        ]);

        // Audit log
        ActivityLog::record(
            action: 'approved',
            subject: $request_item,
            oldStatus: $oldStatus,
            newStatus: 'processing',
            description: "Request approved by " . Auth::user()->name,
        );

        return back()->with('success', 'Request approved and marked as processing.');
    }

    // Release → released (admin & staff)
    public function release(DocumentRequest $request_item) {
        $oldStatus = $request_item->status;

        $request_item->update([
            'status'      => 'released',
            'released_at' => now(),
        ]);

        // Audit log
        ActivityLog::record(
            action: 'released',
            subject: $request_item,
            oldStatus: $oldStatus,
            newStatus: 'released',
            description: "Document released by " . Auth::user()->name,
        );

        return back()->with('success', 'Document marked as released.');
    }

    // Reject → rejected (admin & staff)
    public function reject(Request $request, DocumentRequest $request_item) {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $oldStatus = $request_item->status;

        $request_item->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'processed_by'     => Auth::id(),
        ]);

        // Audit log
        ActivityLog::record(
            action: 'rejected',
            subject: $request_item,
            oldStatus: $oldStatus,
            newStatus: 'rejected',
            description: "Request rejected by " . Auth::user()->name . ". Reason: {$validated['rejection_reason']}",
        );

        return back()->with('success', 'Request has been rejected.');
    }

    // Delete (admin only — enforced via route middleware)
    public function destroy(DocumentRequest $request_item) {
        // Audit log before deletion
        ActivityLog::record(
            action: 'deleted',
            subject: $request_item,
            oldStatus: $request_item->status,
            description: "Request #{$request_item->id} (tracking: {$request_item->tracking_code}) deleted by " . Auth::user()->name,
        );

        $request_item->delete();
        return redirect()->route('requests.index')->with('success', 'Request deleted.');
    }

    // Print Layout preview
    public function print(DocumentRequest $request_item) {
        $request_item->load(['resident', 'documentType']);
        return view('requests.print', compact('request_item'));
    }

    // Real-time polling API endpoint for checking global entry counters
    public function checkUpdates() {
        return response()->json([
            'pending' => DocumentRequest::where('status', 'pending')->count(),
            'total'   => DocumentRequest::count(),
            'time'    => now()->format('h:i:s A'),
        ]);
    }
}
