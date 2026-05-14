<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourierResource\Pages;
use App\Models\Courier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourierResource extends Resource
{
    protected static ?string $model = Courier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string { return __('Couriers'); }
    public static function getModelLabel(): string { return __('Courier'); }
    public static function getPluralModelLabel(): string { return __('Couriers'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label(__('Nombre del courier'))
                ->placeholder('DHL, UPS, FedEx, MRW...')
                ->required()
                ->maxLength(100)
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
            ->emptyStateHeading(__('Sin couriers'))
            ->emptyStateDescription(__('Añade los couriers que utilizas para envíos a buques.'))
            ->emptyStateIcon('heroicon-o-truck');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCouriers::route('/'),
            'create' => Pages\CreateCourier::route('/create'),
            'edit'   => Pages\EditCourier::route('/{record}/edit'),
        ];
    }
}
