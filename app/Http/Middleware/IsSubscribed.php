<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Contracts\Auth\Guard;
use Auth;
use Illuminate\Support\Facades\DB;

class IsSubscribed
{
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }
    
    public function handle($request, Closure $next)
    {
        $user = null;
        $api_token = $request->api_token;
        if (!$api_token)
        {
            if (!Auth::check())
            {
                return redirect()->intended('/login');
            }
            $user = Auth::user();
        }
        else
        {
            $user = \App\User::findByApiToken ($api_token);
            if (!$user)
            {
                return response('Forbidden.', 403);
            }
        }
        
        if ($user->subscribed == '1')
        {
            return $next($request);
        }
        if ($request->ajax())
        {
            return response('Forbidden.', 403);
        }
        throw new AccessDeniedHttpException;
        //return $next($request);
    }
}
