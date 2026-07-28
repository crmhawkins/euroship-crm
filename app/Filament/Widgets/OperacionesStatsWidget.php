<?php

namespace App\Filament\Widgets;

use App\Models\Servicio;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperacionesStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        try {
            $serviciosSinLlegada = Servicio::whereNull('llegada')->count();
            $serviciosIncidencia = Servicio::where('incidencia', true)->count();
        } catch (\Throwable $e) {
            $serviciosSinLlegada = $serviciosIncidencia = '—';
        }

        return [
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
