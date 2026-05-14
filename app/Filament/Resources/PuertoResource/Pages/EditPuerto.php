<?php

namespace App\Filament\Resources\PuertoResource\Pages;

use App\Filament\Resources\PuertoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPuerto extends EditRecord
{
    protected static string $resource = PuertoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
