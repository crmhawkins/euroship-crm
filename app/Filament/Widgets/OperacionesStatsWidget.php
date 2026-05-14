<?php

namespace App\Filament\Widgets;

use App\Models\Presupuesto;
use App\Models\Servicio;
use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperacionesStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $pedidosPendParcial = Pedido::whereIn('estado_general', ['pendiente', 'parcial'])->count();
        $presupuestosPend   = Presupuesto::where('estado', 'ofertado')->count();
        $serviciosSinLlegada = Servicio::whereNull('llegada')->count();

        return [
            Stat::make(__('Pedidos pendientes/parciales'), $pedidosPendParcial)
                ->description(__('Sin completar entrega'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Presupuestos ofertados'), $presupuestosPend)
                ->description(__('Pendientes de respuesta'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            Stat::make(__('Servicios sin llegada'), $serviciosSinLlegada)
                ->description(__('Conocimientos pendientes de recibir'))
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('danger'),
        ];
    }
}
