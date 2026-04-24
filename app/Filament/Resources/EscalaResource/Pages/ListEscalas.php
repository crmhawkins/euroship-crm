<?php

namespace App\Filament\Resources\EscalaResource\Pages;

use App\Filament\Resources\EscalaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEscalas extends ListRecords
{
    protected static string $resource = EscalaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
