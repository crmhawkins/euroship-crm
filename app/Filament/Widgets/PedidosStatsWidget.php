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

        $entregasSemana = Pertrecho::where('estado', 'entregado')
            ->whereBetween('updated_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(updated_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c')
            ->all();

        return [
            Stat::make(__('Pedidos pendientes'), $pendientes)
                ->description(__('Sin iniciar entrega'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->icon('heroicon-o-clipboard-document-list'),

            Stat::make(__('Pedidos parciales'), $parciales)
                ->description(__('Con entrega parcial'))
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make(__('Pedidos entregados'), $entregados)
                ->description(__('Totalmente completados'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('Entregas hoy'), $entregasHoy)
                ->description(__('Pertrechos entregados en las últimas 24 h'))
                ->descriptionIcon('heroicon-m-truck')
                ->chart($entregasSemana ?: [0, 0, 0, 0, 0, 0, 0])
                ->color('primary'),
        ];
    }
}
