<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enable API to accept cookies and sessions
        $middleware->statefulApi();
        
        // Configure CORS for cookie-based authentication
        $middleware->validateCsrfTokens(except: [
            // Exempt routes if needed (not recommended for production)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
