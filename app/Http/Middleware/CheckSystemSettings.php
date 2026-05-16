<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CheckSystemSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceMode = Setting::where('key', 'maintenance_mode')->first()?->value === 'true';
        
        if ($maintenanceMode && !$request->routeIs('public.maintenance')) {
            return redirect()->route('public.maintenance');
        }

        if (!$maintenanceMode && $request->routeIs('public.maintenance')) {
            return redirect()->route('public.home');
        }

        $allowOnlineRequests = Setting::where('key', 'allow_online_requests')->first()?->value === 'true';

        if (!$allowOnlineRequests && in_array($request->route()->getName(), ['public.request', 'public.submit'])) {
            return redirect()->route('public.home')->with('error', 'Online document requests are currently disabled by the administration. Please visit the barangay hall.');
        }

        return $next($request);
    }
}
