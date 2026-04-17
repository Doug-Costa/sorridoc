<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RhSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = session('rh_company_id');
        $token = session('rh_token');
        $uriToken = $request->route('token');

        if (! $companyId || ! $token || $token !== $uriToken) {
            return redirect()->route('rh.login', $uriToken);
        }

        return $next($request);
    }
}
