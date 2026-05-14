<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarcoResource\Pages;
use App\Models\Barco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BarcoResource extends Resource
{
    protected static ?string $model = Barco::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $navigationGroup = 'Maestros';

    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('Barcos');
    }

    public static function getModelLabel(): string
    {
        return __('Barco');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Barcos');
    }

    public static function form(Form $form): Form
    {
        // Layout plan:
        // Row 1 (Grid 2): [Titularidad] [Identificación marítima]
        // Row 2: Notas (collapsible, collapsed)
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Section::make(__('Titularidad'))
                    ->icon('heroicon-o-building-office-2')
                    ->description(__('Cliente propietario y nombre del buque.'))
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label(__('Cliente'))
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('nombre')
                            ->label(__('Nombre del barco'))
                            ->placeholder('M/V Atlantic Star')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Identificación marítima'))
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Forms\Components\TextInput::make('imo_number')
                            ->label(__('Número IMO'))
                            ->placeholder('IMO 9876543')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('bandera')
                            ->label(__('Bandera'))
                            ->placeholder('España, Panamá...')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('tipo')
                            ->label(__('Tipo de buque'))
                            ->placeholder('Bulk carrier, Container...')
                            ->maxLength(100)
                            ->columnSpanFull(),
                    ]),
            ]),

            Forms\Components\Section::make(__('Notas internas'))
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    Forms\Components\Textarea::make('notas')
                        ->hiddenLabel()
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label(__('Barco'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label(__('Cliente'))
                    ->searchable()
                    ->sortable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('imo_number')
                    ->label(__('IMO'))
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('bandera')
                    ->label(__('Bandera'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipo')
                    ->label(__('Tipo'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('escalas_count')
                    ->label(__('Escalas'))
                    ->counts('escalas')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->label(__('Cliente'))
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip(__('Ver')),
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('nombre')
            ->emptyStateHeading(__('Sin barcos registrados'))
            ->emptyStateDescription(__('Da de alta el primer barco asociado a un cliente.'))
            ->emptyStateIcon('heroicon-o-globe-europe-africa');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'imo_number', 'cliente.nombre'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBarcos::route('/'),
            'create' => Pages\CreateBarco::route('/create'),
            'view'   => Pages\ViewBarco::route('/{record}'),
            'edit'   => Pages\EditBarco::route('/{record}/edit'),
        ];
    }
}
