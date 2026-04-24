<?php

namespace App\Filament\Resources\BarcoResource\Pages;

use App\Filament\Resources\BarcoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarco extends EditRecord
{
    protected static string $resource = BarcoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
