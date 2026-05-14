<?php

namespace App\Filament\Resources\EstatusAduaneroResource\Pages;

use App\Filament\Resources\EstatusAduaneroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstatusAduanero extends EditRecord
{
    protected static string $resource = EstatusAduaneroResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
