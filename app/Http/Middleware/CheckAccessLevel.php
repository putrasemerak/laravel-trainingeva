<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredLevel = null): Response
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        $userLevel = session('aLevel', '');
        $userLevelPrefix = trim(substr($userLevel, 0, 2));

        if ($requiredLevel === 'admin' && $userLevelPrefix !== '01') {
            return redirect('evaluations')->with('error', 'Unauthorized access.');
        }

        // Add more levels if needed

        return $next($request);
    }
}
