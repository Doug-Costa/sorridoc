<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkerSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $workerId = session('worker_id');
        $cpf = session('worker_cpf');
        $token = session('worker_token');

        if (! $workerId || ! $cpf || ! $token) {
            return redirect()->route('worker.login');
        }

        return $next($request);
    }
}
