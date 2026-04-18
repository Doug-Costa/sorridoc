<?php

use App\Http\Middleware\RhSessionMiddleware;
use App\Http\Middleware\UploadSessionMiddleware;
use App\Http\Middleware\WorkerSessionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'rh.session' => RhSessionMiddleware::class,
            'worker.session' => WorkerSessionMiddleware::class,
            'upload.session' => UploadSessionMiddleware::class,
            'portal.access' => \App\Http\Middleware\PortalAccessMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
