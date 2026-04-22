<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // SuperUser bypasses everything
        if ($user->role === 'superuser') {
            return $next($request);
        }

        // Check if permission exists in the database
        $isAllowed = Permission::where('role', $user->role)
            ->where('module', $module)
            ->where('is_allowed', true)
            ->exists();

        if ($isAllowed) {
            return $next($request);
        }

        abort(403, 'Unauthorized action for module: ' . $module);
    }
}
