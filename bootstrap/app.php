<?php

use App\Http\Middleware\CheckMemberSession;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'member.session' => CheckMemberSession::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request): string {
            $user = $request->user();
            if (! $user) {
                return '/login';
            }

            return match ($user->role) {
                'admin' => '/admin/dashboard',
                'petugas' => '/petugas/transaksi',
                'owner' => '/owner/laporan',
                default => '/login',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
