<?php

namespace App\Filament\Widgets;

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
        return __('Pedidos pendientes o parciales');
    }

    public function getTableDescription(): ?string
    {
        return __('Pedidos sin completar entrega, ordenados por fecha más reciente.');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pedido::query()
                    ->whereIn('estado_general', ['pendiente', 'parcial'])
                    ->with(['escala.barco.cliente'])
                    ->orderBy('fecha_pedido', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('numero_pedido')
                    ->label(__('Número'))
                    ->searchable()
                    ->weight('medium'),
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
                Tables\Columns\TextColumn::make('pertrechos_count')
                    ->label(__('Líneas'))
                    ->counts('pertrechos')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('estado_general')
                    ->label(__('Estado'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __(ucfirst($state)))
                    ->color(fn ($state) => $state === 'parcial' ? 'info' : 'warning')
                    ->icon(fn ($state) => $state === 'parcial' ? 'heroicon-m-arrow-path' : 'heroicon-m-clock'),
            ])
            ->paginated([5, 10, 25])
            ->emptyStateHeading(__('Sin pedidos pendientes'))
            ->emptyStateDescription(__('Todo al día.'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
