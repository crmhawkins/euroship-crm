<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use App\Filament\Support\NotificarCliente;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServicio extends ViewRecord
{
    protected static string $resource = ServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            NotificarCliente::configurar(Actions\Action::make('notificar_cliente')),
            Actions\EditAction::make(),
        ];
    }
}
