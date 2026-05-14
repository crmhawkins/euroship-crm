<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UbicacionResource\Pages;
use App\Models\Ubicacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UbicacionResource extends Resource
{
    protected static ?string $model = Ubicacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 92;

    public static function getNavigationLabel(): string { return __('Ubicaciones'); }
    public static function getModelLabel(): string { return __('Ubicación'); }
    public static function getPluralModelLabel(): string { return __('Ubicaciones'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label(__('Nombre de la ubicación'))
                ->placeholder('Almacén A · Estantería 3 · Patio...')
                ->required()
                ->maxLength(150)
                ->unique(ignoreRecord: true)
                ->columnSpan(1),
            Forms\Components\Toggle::make('activo')
                ->label(__('Activo'))
                ->default(true)
                ->onColor('success')
                ->inline(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('Nombre'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\IconColumn::make('activo')
                    ->label(__('Activo'))
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')->label(__('Activo')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('nombre')
            ->emptyStateHeading(__('Sin ubicaciones'))
            ->emptyStateDescription(__('Crea ubicaciones para localizar la mercancía dentro del almacén.'))
            ->emptyStateIcon('heroicon-o-archive-box');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUbicaciones::route('/'),
            'create' => Pages\CreateUbicacion::route('/create'),
            'edit'   => Pages\EditUbicacion::route('/{record}/edit'),
        ];
    }
}
