<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if (! in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized action for your role.'], 403);
            }

            // Redirect to their respective authorized home
            return match ($user->role) {
                'student' => redirect()->route('student.dashboard')->with('error', 'Unauthorized area.'),
                'management' => redirect()->route('management.dashboard')->with('error', 'Unauthorized area.'),
                'admin' => redirect()->route('admin.dashboard')->with('error', 'Unauthorized area.'),
                default => abort(403, 'Unauthorized.'),
            };
        }

        return $next($request);
    }
}
