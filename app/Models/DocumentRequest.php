<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\NewDocumentRequestNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DocumentRequest extends Model {
    protected $fillable = [
        'tracking_code', 'resident_id', 'document_type_id', 'purpose',
        'status', 'rejection_reason', 'processed_by', 'released_at',
        'ip_address', 'user_agent',
    ];

    /**
     * Generate a unique tracking code like BDRS-A1B2C3D4E5
     *
     * Uses 5 bytes (10 hex chars) from CSPRNG for ~1.1 trillion possible codes.
     * This makes brute-force enumeration impractical.
     */
    public static function generateTrackingCode(): string {
        do {
            $code = 'BDRS-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        } while (self::where('tracking_code', $code)->exists());
        return $code;
    }

    protected $casts = [
        'released_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (DocumentRequest $documentRequest) {
            $documentRequest->loadMissing(['resident', 'documentType']);

            $admins = User::query()->admin()->get();

            if ($admins->isEmpty()) {
                return;
            }

            try {
                Notification::send($admins, new NewDocumentRequestNotification($documentRequest));
            } catch (\Throwable $e) {
                Log::warning('Skipping admin notification broadcast because the realtime service is unavailable.', [
                    'document_request_id' => $documentRequest->id,
                    'tracking_code' => $documentRequest->tracking_code,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    // Status constants
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_RELEASED   = 'released';
    const STATUS_REJECTED   = 'rejected';

    public function resident() {
        return $this->belongsTo(Resident::class);
    }

    public function documentType() {
        return $this->belongsTo(DocumentType::class);
    }

    public function processedBy() {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function activityLogs() {
        return ActivityLog::where('subject_type', self::class)
            ->where('subject_id', $this->id)
            ->latest()
            ->get();
    }

    // Helper: badge color per status (for Blade views)
    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'pending'    => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'released'   => 'bg-green-100 text-green-800',
            'rejected'   => 'bg-red-100 text-red-800',
            default      => 'bg-gray-100 text-gray-800',
        };
    }
}
