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

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 91;

    public static function getNavigationLabel(): string { return __('Puertos'); }
    public static function getModelLabel(): string { return __('Puerto'); }
    public static function getPluralModelLabel(): string { return __('Puertos'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label(__('Nombre del puerto'))
                ->placeholder('Algeciras, Valencia, Barcelona...')
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
            ->emptyStateHeading(__('Sin puertos'))
            ->emptyStateDescription(__('Añade los puertos donde operáis.'))
            ->emptyStateIcon('heroicon-o-building-library');
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
