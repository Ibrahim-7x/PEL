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
            'mentions',
            'mentions/*/read',
            'search-usernames',
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Schedule the mention expiration command to run daily
        $schedule->command('mentions:expire-old')
            ->daily()
            ->description('Expire old mention notifications older than 1 day');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
