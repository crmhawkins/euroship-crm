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

// PDF nota de entrega — protegido por auth Filament
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/servicio/{servicio}/nota-entrega', [\App\Http\Controllers\NotaEntregaController::class, 'pdf'])
        ->name('servicio.nota-entrega');
    Route::get('/escala/{escala}/reporte-pendientes', [\App\Http\Controllers\ReporteEscalaController::class, 'pendientes'])
        ->name('escala.reporte-pendientes');
});
