<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMemberSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('member_id')) {
            return redirect()->route('member.login');
        }

        return $next($request);
    }
}
