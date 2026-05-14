<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstatusAduaneroResource\Pages;
use App\Models\EstatusAduanero;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EstatusAduaneroResource extends Resource
{
    protected static ?string $model = EstatusAduanero::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 93;

    public static function getNavigationLabel(): string { return 'Estatus Aduaneros'; }
    public static function getModelLabel(): string { return 'Estatus Aduanero'; }
    public static function getPluralModelLabel(): string { return 'Estatus Aduaneros'; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')->required()->maxLength(150)->unique(ignoreRecord: true),
            Forms\Components\Toggle::make('activo')->default(true)->inline(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('activo')->boolean()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('nombre');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEstatusAduaneros::route('/'),
            'create' => Pages\CreateEstatusAduanero::route('/create'),
            'edit'   => Pages\EditEstatusAduanero::route('/{record}/edit'),
        ];
    }
}
