<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Jika user belum diverifikasi oleh admin
        if ($user && !$user->is_verified_by_admin) {
            Auth::logout();
            
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is pending approval by an administrator. Please contact support.',
            ]);
        }

        return $next($request);
    }
}