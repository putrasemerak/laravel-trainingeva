<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ProgramPermission;
use Illuminate\Support\Facades\Auth;

class CheckLegacyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $progId
     * @param  string  $minLevel (comma separated levels allowed, e.g., '02,03')
     */
    public function handle(Request $request, Closure $next, string $progId, string $minLevel): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Superusers bypass checks
        if ($user->isSuperUser()) {
            return $next($request);
        }

        $allowedLevels = explode(',', $minLevel);

        $permission = ProgramPermission::where('EmpNo', $user->EmpNo)
            ->where('ProgID', $progId)
            ->first();

        if (!$permission || !in_array($permission->ALevel, $allowedLevels)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized legacy access level.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access ' . $progId);
        }

        return $next($request);
    }
}
