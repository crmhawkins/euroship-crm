<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PedidoResource;
use App\Models\Pedido;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PedidosPendientesTableWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return __('Pedidos en curso');
    }

    public function getTableDescription(): ?string
    {
        return __('Pedidos sin entrega completa, ordenados por fecha más reciente.');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pedido::query()
                    ->whereNotIn('estado_general', ['entregado'])
                    ->with(['escala.barco.cliente'])
                    ->orderBy('fecha_pedido', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('numero_pedido')
                    ->label(__('Número'))
                    ->searchable()
                    ->weight('medium')
                    ->color('primary')
                    ->url(fn (Pedido $record) => PedidoResource::getUrl('view', ['record' => $record])),
                Tables\Columns\TextColumn::make('fecha_pedido')
                    ->label(__('Fecha'))
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')
                    ->label(__('Cliente'))
                    ->color('gray'),
                Tables\Columns\TextColumn::make('escala.barco.nombre')
                    ->label(__('Barco')),
                Tables\Columns\TextColumn::make('puerto_entrega')
                    ->label(__('Entrega'))
                    ->icon('heroicon-m-map-pin'),
                Tables\Columns\TextColumn::make('estado_general')
                    ->label(__('Estado'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendiente'         => __('Pendiente'),
                        'preparado'         => __('Preparado'),
                        'facturado'         => __('Facturado'),
                        'despachado'        => __('Despachado'),
                        'entregado_parcial' => __('Entregado parcial'),
                        default             => __(ucfirst($state)),
                    })
                    ->color(fn ($state) => match ($state) {
                        'pendiente'         => 'warning',
                        'preparado'         => 'info',
                        'facturado'         => 'gray',
                        'despachado'        => 'primary',
                        'entregado_parcial' => 'warning',
                        default             => 'gray',
                    })
                    ->icon(fn ($state) => match ($state) {
                        'pendiente'         => 'heroicon-m-clock',
                        'preparado'         => 'heroicon-m-check',
                        'facturado'         => 'heroicon-m-banknotes',
                        'despachado'        => 'heroicon-m-truck',
                        'entregado_parcial' => 'heroicon-m-arrow-path',
                        default             => null,
                    }),
            ])
            ->paginated([5, 10, 25])
            ->emptyStateHeading(__('Sin pedidos en curso'))
            ->emptyStateDescription(__('Todo al día.'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
