<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    /**
     * Handle an incoming request and assign a unique trace ID.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID') ?? (string) Str::uuid();

        // Pass to current request for internal consumers
        $request->headers->set('X-Request-ID', $requestId);

        // Standard context for all Laravel standard logs
        Log::withContext([
            'request_id' => $requestId,
            'service' => config('app.name', 'larashiz'),
            'env' => config('app.env', 'production'),
        ]);

        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
