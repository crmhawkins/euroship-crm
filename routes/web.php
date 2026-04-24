<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Cambio rápido de idioma: /locale/es o /locale/en
Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['es', 'en'], true)) {
        session(['locale' => $lang]);
        if (auth()->check()) {
            $user = auth()->user();
            $user->locale = $lang;
            $user->save();
        }
    }
    return redirect()->back();
})->name('locale.switch');
