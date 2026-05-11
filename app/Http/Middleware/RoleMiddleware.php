<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // Si pas authentifié
        if (!Auth::check()) {
            // Pour Postman (requêtes avec Bearer Token ou qui attendent du JSON)
            if ($request->bearerToken() || $request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
            return redirect('/login');
        }

        $userRole = Auth::user()->role;

       
        if ($userRole !== $role) {
            // Pour Postman
            if ($request->bearerToken() || $request->expectsJson()) {
                return response()->json(['error' => 'Accès non autorisé - Rôle ' . $role . ' requis'], 403);
            }
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}