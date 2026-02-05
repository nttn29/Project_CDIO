<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get user info from token/session
        $userRole = $request->header('X-User-Role');

        if (!in_array($userRole, ['nhan_vien', 'quan_ly', 'admin'])) {
            return response()->json(['error' => 'Only staff can access this resource'], 403);
        }

        return $next($request);
    }
}
