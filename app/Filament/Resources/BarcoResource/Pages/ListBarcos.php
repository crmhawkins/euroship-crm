<?php

namespace App\Filament\Resources\BarcoResource\Pages;

use App\Filament\Resources\BarcoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBarcos extends ListRecords
{
    protected static string $resource = BarcoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
