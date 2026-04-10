<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureUserIsSuperAdmin {
    public function handle(Request $request, Closure $next): Response {
        if (!$request->user() || !$request->user()->isSuperAdmin()) abort(403,'Super administrator privileges required.');
        return $next($request);
    }
}
