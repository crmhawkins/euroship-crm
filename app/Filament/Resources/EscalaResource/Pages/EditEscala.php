<?php

namespace App\Filament\Resources\EscalaResource\Pages;

use App\Filament\Resources\EscalaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEscala extends EditRecord
{
    protected static string $resource = EscalaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
