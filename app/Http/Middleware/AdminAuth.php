<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!session()->has('LoggedAdmin') &&
            ($request->path() != 'users/login' &&
                !$request->routeIs('auth-user-check') &&
                !$request->routeIs('regenerate-otp') &&
                !$request->routeIs('password/reset') &&
                $request->path() != 'users/forgot-password')) {

            Session::put('url.intended', $request->url());

            return redirect('/users/login')->with('fail', 'You must be logged in');
        }

        if ((session()->has('LoggedAdmin') && ($request->path() == 'users/login' ||  $request->routeIs('auth-user-check')))) {

            Session::put('url.intended', $request->url());

            return redirect('/');
        }

        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        return $response;

    }
}
