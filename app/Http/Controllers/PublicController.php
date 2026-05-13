<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PublicController extends Controller {

    // Public landing page
    public function home() {
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        return view('public.home', compact('documentTypes'));
    }

    // Show the request form
    public function requestForm() {
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();
        return view('public.request', compact('documentTypes'));
    }

    // Handle request submission (rate-limited via route middleware)
    public function submitRequest(Request $request) {
        // ─── Honeypot Check ───
        // If the hidden 'website' field is filled, it's a bot
        if ($request->filled('website')) {
            // Silently redirect to success-looking page to fool the bot
            return redirect()->route('public.home');
        }

        $validated = $request->validate([
            'first_name'       => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|max:100',
            'address'          => 'required|string|in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5,Purok 6',
            'contact_number'   => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'birthdate'        => 'nullable|date|before:today',
            'gender'           => 'nullable|in:Male,Female',
            'civil_status'     => 'nullable|in:Single,Married,Widowed,Separated',
            'document_type_id' => 'required|exists:document_types,id',
            'purpose'          => 'required|string|max:500',
        ], [
            'contact_number.regex' => 'Contact number must be a valid PH mobile number (e.g., 09171234567).',
        ]);

        // Find or create the resident using only validated fields
        $resident = Resident::firstOrCreate(
            [
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
            ],
            [
                'middle_name'    => $validated['middle_name'] ?? null,
                'address'        => $validated['address'],
                'contact_number' => $validated['contact_number'] ?? null,
                'birthdate'      => $validated['birthdate'] ?? null,
                'gender'         => $validated['gender'] ?? null,
                'civil_status'   => $validated['civil_status'] ?? null,
            ]
        );

        // Update info if resident already exists (keeps data current)
        $resident->update([
            'address'        => $validated['address'],
            'contact_number' => $validated['contact_number'] ?? $resident->contact_number,
            'middle_name'    => $validated['middle_name'] ?? $resident->middle_name,
            'gender'         => $validated['gender'] ?? $resident->gender,
            'civil_status'   => $validated['civil_status'] ?? $resident->civil_status,
        ]);

        // ─── Business Rule: Prevent duplicate & excessive requests ───

        // 1. Check if this resident already has a pending/processing request for the same document type
        $duplicateExists = DocumentRequest::where('resident_id', $resident->id)
            ->where('document_type_id', $validated['document_type_id'])
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($duplicateExists) {
            $docTypeName = DocumentType::find($validated['document_type_id'])->name ?? 'this document';
            return back()->withInput()->withErrors([
                'document_type_id' => "You already have a pending or processing request for {$docTypeName}. Please wait until it is completed or rejected before submitting a new one.",
            ]);
        }

        // 2. Limit to max 2 requests per day per resident
        $todayCount = DocumentRequest::where('resident_id', $resident->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 2) {
            return back()->withInput()->withErrors([
                'throttle' => 'You can only submit up to 2 document requests per day. Please try again tomorrow.',
            ]);
        }

        // Create the document request with tracking code + IP logging
        $docRequest = DocumentRequest::create([
            'tracking_code'    => DocumentRequest::generateTrackingCode(),
            'resident_id'      => $resident->id,
            'document_type_id' => $validated['document_type_id'],
            'purpose'          => $validated['purpose'],
            'status'           => 'pending',
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
        ]);

        // Log the creation in audit trail
        ActivityLog::record(
            action: 'created',
            subject: $docRequest,
            newStatus: 'pending',
            description: "Public request submitted by {$resident->full_name}",
        );

        try {
            \App\Events\DocumentRequestCreated::dispatch($docRequest);
        } catch (\Throwable $e) {
            // Broadcasting is non-critical — don't block the request if Reverb is offline
        }

        return redirect()->route('public.success', $docRequest->tracking_code);
    }

    // Success page with tracking code
    public function success(string $trackingCode) {
        $docRequest = DocumentRequest::with(['resident', 'documentType'])
            ->where('tracking_code', $trackingCode)
            ->firstOrFail();

        return view('public.success', compact('docRequest'));
    }

    // Show tracking form
    public function trackForm() {
        return view('public.track');
    }

    // Handle tracking lookup (rate-limited via route middleware)
    public function track(Request $request) {
        $validated = $request->validate([
            'tracking_code' => 'required|string|max:20',
        ]);

        $docRequest = DocumentRequest::with(['resident', 'documentType', 'processedBy'])
            ->where('tracking_code', strtoupper($validated['tracking_code']))
            ->first();

        return view('public.track', compact('docRequest'));
    }
}
