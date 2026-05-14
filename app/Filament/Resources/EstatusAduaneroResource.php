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

    public static function getNavigationLabel(): string { return __('Estatus aduaneros'); }
    public static function getModelLabel(): string { return __('Estatus aduanero'); }
    public static function getPluralModelLabel(): string { return __('Estatus aduaneros'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->label(__('Nombre del estatus'))
                ->placeholder('T1, T2, Despachado, Pendiente DUA...')
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
            ->emptyStateHeading(__('Sin estatus aduaneros'))
            ->emptyStateDescription(__('Añade los estatus aduaneros que utilizas en los servicios.'))
            ->emptyStateIcon('heroicon-o-shield-check');
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
