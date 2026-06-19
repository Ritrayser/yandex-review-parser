<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOrganizationOwner
{
    public function handle(Request $request, Closure $next)
    {
        $organization = $request->route('organization');

        if ($organization && $organization->user_id !== auth()->id()) {
            return response()->json(['error' => 'Нет доступа'], 403);
        }

        return $next($request);
    }
}
