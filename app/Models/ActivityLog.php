<?php

namespace App\Models;

use App\Events\ActivityLogCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model {
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'old_status', 'new_status', 'description', 'ip_address', 'user_agent',
    ];

    protected static function booted(): void
    {
        static::created(function (ActivityLog $activityLog) {
            $activityLog->loadMissing('user');
            static::dispatchRealtimeUpdate($activityLog);
        });
    }

    protected static function dispatchRealtimeUpdate(ActivityLog $activityLog): void
    {
        try {
            Event::dispatch(new ActivityLogCreated($activityLog));
        } catch (\Throwable $e) {
            Log::warning('Skipping activity log broadcast because the realtime service is unavailable.', [
                'activity_log_id' => $activityLog->id,
                'action' => $activityLog->action,
                'error' => $e->getMessage(),
            ]);
        }
    }

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

    public function getActionTitleAttribute(): string
    {
        return ucfirst($this->action);
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created' => 'created',
            'released' => 'released',
            'approved', 'processing' => 'approved',
            'rejected' => 'rejected',
            default => 'info',
        };
    }

    public function getTransitionLabelAttribute(): ?string
    {
        if ($this->old_status && $this->new_status) {
            return "{$this->old_status} -> {$this->new_status}";
        }

        if ($this->new_status) {
            return "-> {$this->new_status}";
        }

        return null;
    }

    public function toDashboardPayload(): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'title' => $this->action_title,
            'description' => $this->description,
            'action_badge' => $this->action_badge,
            'action_icon' => $this->action_icon,
            'transition_label' => $this->transition_label,
            'created_at_human' => $this->created_at->diffForHumans(),
            'created_at_iso' => $this->created_at->toIso8601String(),
            'user_name' => $this->user?->name,
        ];
    }
}
