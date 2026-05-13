<?php

namespace App\Events;

use App\Models\DocumentRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentRequestCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $documentRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(DocumentRequest $documentRequest)
    {
        $this->documentRequest = $documentRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'DocumentRequestCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->documentRequest->id,
            'resident_name' => $this->documentRequest->resident->full_name,
            'document_name' => $this->documentRequest->documentType->name,
            'created_at' => $this->documentRequest->created_at->diffForHumans(),
        ];
    }
}
