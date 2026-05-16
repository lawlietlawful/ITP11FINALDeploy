<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Register custom role middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'system.settings' => \App\Http\Middleware\CheckSystemSettings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // TEMPORARY DIAGNOSTIC: Show actual error details for debugging
        $exceptions->render(function (\Throwable $e, $request) {
            return response(
                '<h1>DEBUG ERROR</h1>'
                . '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>'
                . '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>'
                . '<p><strong>Class:</strong> ' . get_class($e) . '</p>'
                . '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>',
                500
            );
        });
    })->create();
