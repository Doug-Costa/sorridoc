<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UploadSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = session('upload_company_id');
        $token = session('upload_token');
        $uriToken = $request->route('token');

        if (! $companyId || ! $token || $token !== $uriToken) {
            return redirect()->route('upload.login', $uriToken);
        }

        return $next($request);
    }
}
