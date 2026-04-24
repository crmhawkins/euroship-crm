<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetUserLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = ['es', 'en'];

        // 1) ?lang= en query string → guarda en sesión y perfil.
        if ($request->filled('lang') && in_array($request->get('lang'), $supported, true)) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);

            if (Auth::check()) {
                $user = Auth::user();
                $user->locale = $locale;
                $user->save();
            }
        }

        // 2) Resuelve locale: user → sesión → config.
        $locale = Auth::user()->locale ?? Session::get('locale') ?? config('app.locale');
        if (! in_array($locale, $supported, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
