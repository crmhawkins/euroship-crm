<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotaEntregaController extends Controller
{
    public function pdf(Servicio $servicio)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $servicio->load(['escala.barco.cliente', 'courier', 'ubicacion', 'estatusAduanero']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.nota-entrega', compact('servicio'))
            ->setPaper('a4', 'portrait');

        $filename = 'nota-entrega-' . ($servicio->number ?? $servicio->id) . '.pdf';

        return $pdf->download($filename);
    }
}
