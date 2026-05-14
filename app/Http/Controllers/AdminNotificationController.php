<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'read_at' => now()->toIso8601String(),
        ]);
    }
}
