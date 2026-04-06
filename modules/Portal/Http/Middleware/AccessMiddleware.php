<?php

namespace Modules\Portal\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->hasRole('administrator')) {
            return $next($request);
        }

        if (isLevelOne($user)) {
            return $next($request);
        }

        if (isSuperUser($user)) {
            if (!session()->has('selected_grade')) {
                return redirect()->route('choose.education');
            }
            return $next($request);
        }

        if (Gate::allows('is-casier')) {
            if (!$request->routeIs('portal::dashboard-msdm.index')) {
                return redirect()->route('portal::dashboard-msdm.index');
            }
        }

        if (Gate::allows('is-supplier')) {
            if (!$request->routeIs('poz::supplier.dashboard')) {
                return redirect()->route('poz::supplier.dashboard');
            }
        }

        return $next($request);
    }
}
