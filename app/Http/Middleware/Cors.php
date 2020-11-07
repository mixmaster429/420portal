<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return $next($request)
        //     ->header('Access-Control-Allow-Origin', '*')
        //     ->header('Access-Control-Allow-Headers', 'Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Origin, Authorization, Origin, X-Requested-With')
        //     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $headers = [
            'Access-Control-Allow-Origin'=> '*',
            'Access-Control-Allow-Headers'=> 'Authorization, Content-Type, X-Auth-Token, Origin, X-Request-With',
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
        ];
            
        $response = $next($request);
        foreach($headers as $key => $value)
        $response->header($key, $value);
        
        return $response;
    }
}
