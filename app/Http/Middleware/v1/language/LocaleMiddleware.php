<?php

namespace App\Http\Middleware\v1\language;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userLocale = request()->cookie('locale',  config('app.locale'));
        if (App::getLocale() !== $userLocale) {
            // Set the locale only if it differs
            App::setLocale($userLocale);
        }
        return $next($request);
    }
}
