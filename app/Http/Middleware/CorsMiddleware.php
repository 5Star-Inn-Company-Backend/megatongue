<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
    public function handle($request, Closure $next)
    {
        $response = $next($request);

         $response->header('Access-Control-Allow-Origin', '*');
         $response->header('Access-Control-Allow-Methods', '*');
         $response->header('Access-Control-Allow-Headers', '*');

        return $response;
    }
}
