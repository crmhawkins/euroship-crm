<?php

namespace App\Filament\Resources\BarcoResource\Pages;

use App\Filament\Resources\BarcoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBarco extends ViewRecord
{
    protected static string $resource = BarcoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
