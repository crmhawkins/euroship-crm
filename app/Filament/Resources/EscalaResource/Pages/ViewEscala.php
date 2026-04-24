<?php

namespace App\Filament\Resources\EscalaResource\Pages;

use App\Filament\Resources\EscalaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEscala extends ViewRecord
{
    protected static string $resource = EscalaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
