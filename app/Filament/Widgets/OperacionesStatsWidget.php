<?php

namespace App\Filament\Widgets;

use App\Models\Presupuesto;
use App\Models\Servicio;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperacionesStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        try {
            $presupuestosPend     = Presupuesto::where('estado', 'ofertado')->count();
            $presupuestosAceptados = Presupuesto::where('estado', 'aceptado')->count();
            $serviciosSinLlegada  = Servicio::whereNull('llegada')->count();
            $serviciosIncidencia  = Servicio::where('incidencia', true)->count();
        } catch (\Throwable $e) {
            $presupuestosPend = $presupuestosAceptados = $serviciosSinLlegada = $serviciosIncidencia = '—';
        }

        return [
            Stat::make(__('Presupuestos ofertados'), $presupuestosPend)
                ->description(__('Pendientes de respuesta del cliente'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),

            Stat::make(__('Presupuestos aceptados'), $presupuestosAceptados)
                ->description(__('Presupuestos confirmados'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make(__('Servicios sin llegada'), $serviciosSinLlegada)
                ->description(__('Conocimientos pendientes de recibir'))
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('info'),

            Stat::make(__('Servicios con incidencia'), $serviciosIncidencia)
                ->description(__('Requieren atención'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
