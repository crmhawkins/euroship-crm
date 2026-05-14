<?php

namespace App\Http\Controllers;

use App\Models\Escala;
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
            ->whereIn('estado_general', ['pendiente', 'parcial'])
            ->with('pertrechos')
            ->orderBy('fecha_pedido')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte-pendientes', compact('escala', 'pedidos'))
            ->setPaper('a4', 'portrait');

        $filename = 'reporte-pendientes-escala-' . $escala->id . '.pdf';

        return $pdf->download($filename);
    }
}
