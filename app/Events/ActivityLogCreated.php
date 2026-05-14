<?php

namespace App\Events;

use App\Models\ActivityLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ActivityLog $activityLog
    ) {
        $this->activityLog->loadMissing('user');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ActivityLogCreated';
    }

    public function broadcastWith(): array
    {
        return $this->activityLog->toDashboardPayload();
    }
}
