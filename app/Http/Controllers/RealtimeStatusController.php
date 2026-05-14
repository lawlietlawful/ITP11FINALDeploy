<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

class RealtimeStatusController extends Controller
{
    public function show(): JsonResponse
    {
        $driver = (string) Config::get('broadcasting.default');
        $connection = (array) Config::get("broadcasting.connections.{$driver}", []);
        $options = (array) ($connection['options'] ?? []);

        $host = $this->normalizeHost((string) ($options['host'] ?? ''));
        $port = (int) ($options['port'] ?? 0);
        $configured = $driver === 'reverb'
            && filled($connection['key'] ?? null)
            && filled($connection['secret'] ?? null)
            && filled($connection['app_id'] ?? null)
            && filled($host)
            && $port > 0;

        $reachable = $configured ? $this->canReachSocket($host, $port) : false;

        return response()->json([
            'enabled' => $driver === 'reverb',
            'configured' => $configured,
            'reachable' => $reachable,
            'healthy' => $driver === 'reverb' && $configured && $reachable,
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
        ]);
    }

    private function normalizeHost(string $host): string
    {
        if ($host === '0.0.0.0') {
            return '127.0.0.1';
        }

        return trim($host);
    }

    private function canReachSocket(string $host, int $port): bool
    {
        $timeoutInSeconds = 1.0;
        $socket = @fsockopen($host, $port, $errorNumber, $errorMessage, $timeoutInSeconds);

        if (! is_resource($socket)) {
            return false;
        }

        fclose($socket);

        return true;
    }
}
