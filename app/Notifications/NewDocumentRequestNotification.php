<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewDocumentRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected DocumentRequest $documentRequest
    ) {
        $this->documentRequest->loadMissing(['resident', 'documentType']);
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->documentRequest->id,
            'tracking_code' => $this->documentRequest->tracking_code,
            'resident_name' => $this->documentRequest->resident->full_name,
            'document_name' => $this->documentRequest->documentType->name,
            'purpose' => $this->documentRequest->purpose,
            'status' => $this->documentRequest->status,
            'request_url' => route('requests.index', ['open_request' => $this->documentRequest->id]),
            'created_at_human' => $this->documentRequest->created_at->diffForHumans(),
            'created_at_iso' => $this->documentRequest->created_at->toIso8601String(),
            'message' => "{$this->documentRequest->resident->full_name} requested {$this->documentRequest->documentType->name}.",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
