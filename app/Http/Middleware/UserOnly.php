<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Debug: cek apakah middleware dipanggil
        \Log::info('UserOnly middleware executed', [
            'user_id' => auth()->id(),
            'is_admin' => auth()->check() ? auth()->user()->is_admin : 'not logged in',
            'path' => $request->path()
        ]);

        if (auth()->check() && auth()->user()->is_admin) {
            \Log::info('Redirecting admin to dashboard');
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
