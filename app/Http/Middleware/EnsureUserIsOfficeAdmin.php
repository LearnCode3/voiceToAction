<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureUserIsOfficeAdmin {
    public function handle(Request $request, Closure $next): Response {
        $user = $request->user();
        if (!$user || !$user->canAccessPanel()) abort(403,'Office staff privileges required.');
        if ($user->isBanned()) abort(403,'Your account has been suspended.');
        return $next($request);
    }
}
