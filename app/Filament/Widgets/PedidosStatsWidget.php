<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use App\Models\Pertrecho;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PedidosStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pendientes = Pedido::where('estado_general', 'pendiente')->count();
        $parciales  = Pedido::where('estado_general', 'parcial')->count();
        $entregados = Pedido::where('estado_general', 'entregado')->count();

        $entregasHoy = Pertrecho::where('estado', 'entregado')
            ->whereDate('updated_at', today())
            ->count();

        return [
            Stat::make(__('Pedidos pendientes'), $pendientes)
                ->description(__('Pedidos sin iniciar entrega'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Pedidos parciales'), $parciales)
                ->description(__('Pedidos con entrega parcial'))
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make(__('Pedidos entregados'), $entregados)
                ->description(__('Totalmente entregados'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('Entregas hoy'), $entregasHoy)
                ->description(__('Pertrechos marcados como entregados hoy'))
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
        ];
    }
}
