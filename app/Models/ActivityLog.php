<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model {
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'old_status', 'new_status', 'description', 'ip_address', 'user_agent',
    ];

    /**
     * Log an activity with full context.
     */
    public static function record(
        string $action,
        Model  $subject,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?string $description = null,
    ): self {
        $request = request();

        return self::create([
            'user_id'      => Auth::id(),
            'action'       => $action,
            'subject_type' => get_class($subject),
            'subject_id'   => $subject->getKey(),
            'old_status'   => $oldStatus,
            'new_status'   => $newStatus,
            'description'  => $description,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);
    }

    // ─── Relationships ───

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function subject() {
        return $this->morphTo();
    }

    // ─── Accessors ───

    public function getActionBadgeAttribute(): string {
        return match($this->action) {
            'created'  => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-emerald-100 text-emerald-800',
            'released' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'deleted'  => 'bg-gray-100 text-gray-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }
}
