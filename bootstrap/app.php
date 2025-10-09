<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Register session timeout middleware for web routes
        $middleware->web(\App\Http\Middleware\SessionTimeoutMiddleware::class);

        // Exclude API routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'fetch-coms-data',
            'generate-ticket-number',
            'check-complaint-ticket',
            'home-agent/ticket/*/feedback',
            'agent/feedback/store/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
