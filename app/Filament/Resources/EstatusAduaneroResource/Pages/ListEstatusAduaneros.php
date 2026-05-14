<?php

namespace App\Filament\Resources\EstatusAduaneroResource\Pages;

use App\Filament\Resources\EstatusAduaneroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstatusAduaneros extends ListRecords
{
    protected static string $resource = EstatusAduaneroResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
