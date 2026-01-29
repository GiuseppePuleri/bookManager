<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin{
    public function handle(Request $request, Closure $next): Response{

        // Check if is logged and 'role' è 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        return abort(403, 'Only admin here!');

    }
}
