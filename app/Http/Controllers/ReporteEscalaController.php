<?php

namespace App\Http\Controllers;

use App\Models\Escala;
use App\Models\Estado;
use Illuminate\Support\Facades\Auth;

class ReporteEscalaController extends Controller
{
    public function pendientes(Escala $escala)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $escala->load(['barco.cliente']);

        $pedidos = $escala->pedidos()
            ->whereNotIn('estado_general', Estado::clavesFinalizadas(Estado::TIPO_PEDIDO))
            ->with('pertrechos')
            ->orderBy('fecha_pedido')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte-pendientes', compact('escala', 'pedidos'))
            ->setPaper('a4', 'portrait')
            ->setOption('isFontSubsettingEnabled', true);

        $filename = 'reporte-pendientes-escala-' . $escala->id . '.pdf';

        return $pdf->download($filename);
    }
}
