<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PedidosPendientesTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return __('Pedidos pendientes o parciales');
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
                Tables\Columns\TextColumn::make('numero_pedido')->label(__('Número'))->searchable(),
                Tables\Columns\TextColumn::make('fecha_pedido')->label(__('Fecha'))->date('Y-m-d'),
                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')->label(__('Cliente')),
                Tables\Columns\TextColumn::make('escala.barco.nombre')->label(__('Barco')),
                Tables\Columns\TextColumn::make('puerto_entrega')->label(__('Puerto entrega')),
                Tables\Columns\TextColumn::make('estado_general')
                    ->label(__('Estado'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __(ucfirst($state)))
                    ->color(fn ($state) => $state === 'parcial' ? 'info' : 'warning'),
            ])
            ->paginated([5, 10, 25]);
    }
}
