<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscalaResource\Pages;
use App\Models\Escala;
use App\Models\Puerto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EscalaResource extends Resource
{
    protected static ?string $model = Escala::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('Escalas');
    }

    public static function getModelLabel(): string
    {
        return __('Escala');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Escalas');
    }

    public static function form(Form $form): Form
    {
        // Layout plan:
        // Row 1 (Grid 2): [Asignación: Barco + Fecha] [Puerto]
        // Row 2: Notas (collapsible)
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Section::make(__('Asignación'))
                    ->icon('heroicon-o-link')
                    ->schema([
                        Forms\Components\Select::make('barco_id')
                            ->label(__('Barco'))
                            ->relationship('barco', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombre} — {$record->cliente?->nombre}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('fecha')
                            ->label(__('Fecha de escala'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Puerto'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Select::make('puerto')
                            ->label(__('Puerto'))
                            ->options(fn () => Puerto::activos()->orderBy('nombre')->pluck('nombre', 'nombre'))
                            ->searchable()
                            ->required()
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
                Tables\Columns\TextColumn::make('fecha')
                    ->label(__('Fecha'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('puerto')
                    ->label(__('Puerto'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-map-pin'),
                Tables\Columns\TextColumn::make('barco.nombre')
                    ->label(__('Barco'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('barco.cliente.nombre')
                    ->label(__('Cliente'))
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pedidos_count')
                    ->label(__('Pedidos'))
                    ->counts('pedidos')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('servicios_count')
                    ->label(__('Servicios'))
                    ->counts('servicios')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('presupuestos_count')
                    ->label(__('Presup.'))
                    ->counts('presupuestos')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('barco_id')
                    ->label(__('Barco'))
                    ->relationship('barco', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('reporte_pendientes')
                    ->label(__('PDF'))
                    ->tooltip(__('Reporte de pertrechos pendientes'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->iconButton()
                    ->url(fn (Escala $record) => route('escala.reporte-pendientes', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make()->iconButton()->tooltip(__('Ver')),
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('fecha', 'desc')
            ->emptyStateHeading(__('Sin escalas registradas'))
            ->emptyStateDescription(__('Crea la primera escala para empezar a gestionar pedidos, servicios y presupuestos.'))
            ->emptyStateIcon('heroicon-o-map-pin');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['puerto', 'barco.nombre', 'barco.cliente.nombre'];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEscalas::route('/'),
            'create' => Pages\CreateEscala::route('/create'),
            'view'   => Pages\ViewEscala::route('/{record}'),
            'edit'   => Pages\EditEscala::route('/{record}/edit'),
        ];
    }
}
