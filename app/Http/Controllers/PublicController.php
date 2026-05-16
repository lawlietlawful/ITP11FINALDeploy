<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class PublicController extends Controller {

    public function home() {
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('public.home', compact('documentTypes'));
    }

    public function requestForm() {
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('public.request', compact('documentTypes'));
    }

    public function submitRequest(Request $request) {
        if ($request->filled('website')) {
            $this->logPublicSecurityEvent(
                action: 'blocked',
                description: 'Blocked public request because the honeypot field was filled.',
                request: $request,
            );

            return redirect()->route('public.home');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5,Purok 6',
            'contact_number' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'email' => 'nullable|email|max:255',
            'birthdate' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'gender' => 'nullable|in:Male,Female',
            'civil_status' => 'nullable|in:Single,Married,Widowed,Separated',
            'document_type_id' => [
                'required',
                Rule::exists('document_types', 'id')->where('is_active', true),
            ],
            'purpose' => 'required|string|max:500',
        ], [
            'contact_number.regex' => 'Contact number must be a valid PH mobile number (e.g., 09171234567).',
        ]);

        $identityLockKey = $this->publicRequestCooldownKey($validated);
        if (RateLimiter::tooManyAttempts($identityLockKey, 1)) {
            $seconds = RateLimiter::availableIn($identityLockKey);

            $this->logPublicSecurityEvent(
                action: 'throttled',
                description: "Blocked public request because the identity cooldown is active for another {$seconds} second(s).",
                request: $request,
            );

            return back()
                ->withInput()
                ->withErrors([
                    'throttle' => "Too many submission attempts for the same details. Please wait {$seconds} second(s) before trying again.",
                ]);
        }

        $recentAttempts = RateLimiter::hit($this->publicRequestAttemptsKey($validated), 3600);
        if ($recentAttempts >= 5) {
            RateLimiter::hit($identityLockKey, 600);

            $this->logPublicSecurityEvent(
                action: 'throttled',
                description: 'Applied public request cooldown after repeated submission attempts for the same identity details.',
                request: $request,
            );

            return back()
                ->withInput()
                ->withErrors([
                    'throttle' => 'Too many submission attempts for the same details. Please wait 600 second(s) before trying again.',
                ]);
        }

        $resident = $this->findMatchingResidentOrFail($validated);

        $duplicateExists = DocumentRequest::where('resident_id', $resident->id)
            ->where('document_type_id', $validated['document_type_id'])
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($duplicateExists) {
            $duplicateAttempts = RateLimiter::hit($this->duplicateRequestAttemptsKey($validated), 900);

            if ($duplicateAttempts >= 3) {
                RateLimiter::hit($identityLockKey, 300);
            }

            $this->logPublicSecurityEvent(
                action: 'blocked',
                description: "Blocked duplicate public request attempt for {$resident->full_name}.",
                request: $request,
                subject: $resident,
            );

            $docTypeName = DocumentType::find($validated['document_type_id'])->name ?? 'this document';

            return back()->withInput()->withErrors([
                'document_type_id' => "You already have a pending or processing request for {$docTypeName}. Please wait until it is completed or rejected before submitting a new one.",
            ]);
        }

        $todayCount = DocumentRequest::where('resident_id', $resident->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 2) {
            RateLimiter::hit($identityLockKey, 3600);

            $this->logPublicSecurityEvent(
                action: 'blocked',
                description: "Blocked public request because {$resident->full_name} already reached the daily submission cap.",
                request: $request,
                subject: $resident,
            );

            return back()->withInput()->withErrors([
                'throttle' => 'You can only submit up to 2 document requests per day. Please try again tomorrow.',
            ]);
        }

        $docRequest = DocumentRequest::create([
            'tracking_code' => DocumentRequest::generateTrackingCode(),
            'resident_id' => $resident->id,
            'document_type_id' => $validated['document_type_id'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        ActivityLog::record(
            action: 'created',
            subject: $docRequest,
            newStatus: 'pending',
            description: "Public request submitted by {$resident->full_name}",
        );

        RateLimiter::clear($this->duplicateRequestAttemptsKey($validated));

        return redirect()->route('public.success', $docRequest->tracking_code);
    }

    public function success(string $trackingCode) {
        $docRequest = DocumentRequest::with(['resident', 'documentType'])
            ->where('tracking_code', strtoupper($trackingCode))
            ->firstOrFail();

        return view('public.success', compact('docRequest'));
    }

    public function trackForm() {
        return view('public.track');
    }

    public function track(Request $request) {
        $validated = $request->validate([
            'tracking_code' => ['required', 'string', 'max:20', 'regex:/^BDRS-[A-F0-9]{10}$/i'],
        ]);

        $lockKey = $this->trackingCooldownKey($request);
        if (RateLimiter::tooManyAttempts($lockKey, 1)) {
            $seconds = RateLimiter::availableIn($lockKey);

            return back()
                ->withInput()
                ->withErrors([
                    'tracking_code' => "Too many failed tracking attempts. Please wait {$seconds} second(s) before trying again.",
                ]);
        }

        $docRequest = DocumentRequest::with(['resident', 'documentType', 'processedBy'])
            ->where('tracking_code', strtoupper($validated['tracking_code']))
            ->first();

        if (! $docRequest) {
            $attempts = RateLimiter::hit($this->failedTrackingAttemptsKey($request), 600);

            if ($attempts >= 5) {
                $cooldownSeconds = min(900, 30 * (2 ** min($attempts - 5, 4)));
                RateLimiter::hit($lockKey, $cooldownSeconds);
            }

            return view('public.track', [
                'docRequest' => null,
                'failedTrackingAttempts' => $attempts,
            ]);
        }

        RateLimiter::clear($this->failedTrackingAttemptsKey($request));
        RateLimiter::clear($lockKey);

        return view('public.track', compact('docRequest'));
    }

    private function failedTrackingAttemptsKey(Request $request): string
    {
        return 'public-track-failed:' . sha1((string) $request->ip());
    }

    private function trackingCooldownKey(Request $request): string
    {
        return 'public-track-cooldown:' . sha1((string) $request->ip());
    }

    private function publicRequestAttemptsKey(array $validated): string
    {
        return 'public-request-attempts:' . sha1($this->publicRequestIdentityFingerprint($validated));
    }

    private function duplicateRequestAttemptsKey(array $validated): string
    {
        return 'public-request-duplicate:' . sha1($this->publicRequestIdentityFingerprint($validated) . '|' . $validated['document_type_id']);
    }

    private function publicRequestCooldownKey(array $validated): string
    {
        return 'public-request-cooldown:' . sha1($this->publicRequestIdentityFingerprint($validated));
    }

    private function publicRequestIdentityFingerprint(array $validated): string
    {
        return implode('|', [
            strtolower(trim($validated['first_name'])),
            strtolower(trim((string) ($validated['middle_name'] ?? ''))),
            strtolower(trim($validated['last_name'])),
            strtolower(trim($validated['address'])),
            (string) ($validated['birthdate'] ?? ''),
            strtolower(trim((string) ($validated['gender'] ?? ''))),
            strtolower(trim((string) ($validated['civil_status'] ?? ''))),
            preg_replace('/\D+/', '', (string) ($validated['contact_number'] ?? '')),
        ]);
    }

    private function findMatchingResidentOrFail(array $validated): Resident
    {
        $resident = Resident::query()
            ->where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->whereDate('birthdate', $validated['birthdate'])
            ->first();

        if (! $resident) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'first_name' => 'We could not find a registered resident matching this Name and Birthdate. Please ensure you are officially registered at the Barangay Hall.'
            ]);
        }

        // Keep the database up to date with their latest submission details
        $resident->update([
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'] ?? $resident->contact_number,
            'email' => $validated['email'] ?? $resident->email,
        ]);

        return $resident;
    }

    private function logPublicSecurityEvent(
        string $action,
        string $description,
        Request $request,
        ?Model $subject = null,
    ): void {
        ActivityLog::create([
            'user_id' => null,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : Resident::class,
            'subject_id' => $subject?->getKey() ?? 0,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
