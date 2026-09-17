<?php

namespace App\Filament\Resources\EscalaResource\Pages;

use App\Filament\Resources\EscalaResource;
use App\Filament\Resources\PedidoResource;
use App\Filament\Resources\PresupuestoResource;
use App\Filament\Resources\ServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEscala extends ViewRecord
{
    protected static string $resource = EscalaResource::class;

    protected function getHeaderActions(): array
    {
        $filtro = EscalaResource::filtroEscala($this->getRecord());

        return [
            Actions\Action::make('pedidos')
                ->label(__('Pedidos'))
                ->icon('heroicon-o-clipboard-document-list')
                ->color('gray')
                ->url(PedidoResource::getUrl('index', $filtro)),
            Actions\Action::make('servicios')
                ->label(__('Servicios'))
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('gray')
                ->url(ServicioResource::getUrl('index', $filtro)),
            Actions\Action::make('presupuestos')
                ->label(__('Presupuestos'))
                ->icon('heroicon-o-calculator')
                ->color('gray')
                ->url(PresupuestoResource::getUrl('index', $filtro)),
            Actions\EditAction::make(),
        ];
    }
}
