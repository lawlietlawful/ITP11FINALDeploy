<?php

namespace App\Jobs;

use App\Models\DocumentRequest;
use App\Mail\DocumentReadyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDocumentReadyEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request_item;

    /**
     * Create a new job instance.
     */
    public function __construct(DocumentRequest $request_item)
    {
        $this->request_item = $request_item;
        $this->onConnection('database');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->request_item->resident->email)
                ->send(new DocumentReadyMail($this->request_item));
        } catch (\Exception $e) {
            Log::error("Failed to send Document Ready email in Background Worker: " . $e->getMessage());
        }
    }
}
