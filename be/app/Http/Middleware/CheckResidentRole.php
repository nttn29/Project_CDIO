<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckResidentRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get user info from token/session (in production use JWT)
        $userRole = $request->header('X-User-Role');

        if ($userRole !== 'cu_dan' && $userRole !== 'admin') {
            return response()->json(['error' => 'Only residents can access this resource'], 403);
        }

        return $next($request);
    }
}
