<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuertoResource\Pages;
use App\Models\Puerto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PuertoResource extends Resource
{
    protected static ?string $model = Puerto::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 91;

    public static function getNavigationLabel(): string { return 'Puertos'; }
    public static function getModelLabel(): string { return 'Puerto'; }
    public static function getPluralModelLabel(): string { return 'Puertos'; }

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
            'index'  => Pages\ListPuertos::route('/'),
            'create' => Pages\CreatePuerto::route('/create'),
            'edit'   => Pages\EditPuerto::route('/{record}/edit'),
        ];
    }
}
