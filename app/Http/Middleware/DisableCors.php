<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //return $next($request);
        $response = $next($request);

        if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            // Your additional logic for handling BinaryFileResponse here
            // For example, you might want to disable CORS for binary file downloads
            // or perform specific actions for binary responses.
        }else{
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

       
        return $response;
    }
}
