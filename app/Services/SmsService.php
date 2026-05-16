<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS to a recipient.
     * Currently implemented as a Mock Service for testing.
     * When ready, you can replace this with Semaphore, Twilio, etc.
     */
    public function send(string $number, string $message): bool
    {
        // Remove any non-numeric characters from the phone number
        $cleanNumber = preg_replace('/[^0-9]/', '', $number);

        // Here we simulate sending an SMS by writing it to the Laravel log.
        // You can check storage/logs/laravel.log to see these messages.
        Log::channel('single')->info("MOCK SMS SENT TO [{$cleanNumber}]: {$message}");

        /* 
         * Example Real Integration (Semaphore):
         * 
         * $response = \Illuminate\Support\Facades\Http::post('https://api.semaphore.co/api/v4/messages', [
         *     'apikey' => env('SEMAPHORE_API_KEY'),
         *     'number' => $cleanNumber,
         *     'message' => $message,
         *     'sendername' => env('SEMAPHORE_SENDER_NAME')
         * ]);
         * 
         * return $response->successful();
         */

        return true;
    }
}
