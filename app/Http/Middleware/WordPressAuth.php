<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WordPressAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
          // Check if token exists in session
        if (!$request->session()->has('wp_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
