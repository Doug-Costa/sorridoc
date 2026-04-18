<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PortalAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        $user = Auth::user();

        if (!in_array($user->role, ['Empresa', 'Funcionario'])) {
            Auth::logout();
            return redirect()->route('portal.login')->with('error', 'Acesso negado. Este portal é exclusivo para empresas e funcionários.');
        }

        return $next($request);
    }
}
