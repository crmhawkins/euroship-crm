<?php

namespace App\Http\Controllers;

use App\Models\Escala;
use App\Models\Servicio;
use Illuminate\Support\Facades\Auth;

class NotaEntregaController extends Controller
{
    public function pdf(Servicio $servicio)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $servicio->load(['escala.barco.cliente', 'courier', 'ubicacion', 'estatusAduanero']);
        $escala = $servicio->escala;
        $servicios = collect([$servicio]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.nota-entrega', compact('escala', 'servicios'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('delivery-note-' . ($servicio->number ?? $servicio->id) . '.pdf');
    }

    public function pdfEscala(Escala $escala)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $escala->load(['barco.cliente']);
        $servicios = $escala->servicios()
            ->with(['courier', 'ubicacion', 'estatusAduanero'])
            ->orderBy('llegada')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.nota-entrega', compact('escala', 'servicios'))
            ->setPaper('a4', 'portrait');

        $puerto = str_replace(' ', '-', $escala->puerto ?? $escala->id);
        $fecha  = $escala->fecha?->format('Y-m-d') ?? $escala->id;

        return $pdf->download("delivery-note-{$puerto}-{$fecha}.pdf");
    }
}
