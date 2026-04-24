<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Aplicar locale del usuario autenticado si existe.
        if (Auth::check() && ! empty(Auth::user()->locale)) {
            App::setLocale(Auth::user()->locale);
        }
    }
}
