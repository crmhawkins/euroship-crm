<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresupuestoResource\Pages;
use App\Models\Barco;
use App\Models\Escala;
use App\Models\Presupuesto;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PresupuestoResource extends Resource
{
    protected static ?string $model = Presupuesto::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string { return __('Presupuestos'); }
    public static function getModelLabel(): string { return __('Presupuesto'); }
    public static function getPluralModelLabel(): string { return __('Presupuestos'); }

    public static function form(Form $form): Form
    {
        // Layout plan:
        // Resumen placeholder en edit (Nº · Fecha · Líneas · Total · Estado)
        // Tabs: General (Asignación + Datos lado a lado) | Líneas
        return $form->schema([
            Forms\Components\Placeholder::make('resumen')
                ->label('')
                ->content(function (?Presupuesto $record) {
                    if (! $record) return null;
                    $total = number_format((float) $record->total, 2, ',', '.');
                    return sprintf(
                        '%s · %s · %d líneas · Total: %s € · Estado: %s',
                        $record->numero_presupuesto ?? '—',
                        $record->fecha_presupuesto?->format('d/m/Y'),
                        $record->lineas()->count(),
                        $total,
                        ucfirst($record->estado ?? '—')
                    );
                })
                ->hiddenOn('create')
                ->columnSpanFull(),

            Tabs::make('Presupuesto')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tabs\Tab::make(__('General'))
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Section::make(__('Asignación'))
                                    ->icon('heroicon-o-link')
                                    ->schema([
                                        Forms\Components\Select::make('barco_id')
                                            ->label(__('Barco'))
                                            ->options(fn () => Barco::with('cliente')->get()
                                                ->mapWithKeys(fn ($b) => [$b->id => "{$b->nombre} — {$b->cliente?->nombre}"]))
                                            ->searchable()
                                            ->dehydrated(false)
                                            ->live()
                                            ->afterStateHydrated(function (Set $set, ?Presupuesto $record) {
                                                if ($record?->escala) {
                                                    $set('barco_id', $record->escala->barco_id);
                                                }
                                            })
                                            ->afterStateUpdated(fn (Set $set) => $set('escala_id', null))
                                            ->required(),

                                        Forms\Components\Select::make('escala_id')
                                            ->label(__('Escala'))
                                            ->options(function (Get $get) {
                                                $barcoId = $get('barco_id');
                                                if (! $barcoId) return [];
                                                return Escala::where('barco_id', $barcoId)
                                                    ->orderBy('fecha', 'desc')
                                                    ->get()
                                                    ->mapWithKeys(fn ($e) => [
                                                        $e->id => $e->puerto . ' (' . ($e->fecha?->format('d/m/Y') ?? '—') . ')',
                                                    ]);
                                            })
                                            ->searchable()
                                            ->required(),
                                    ]),

                                Forms\Components\Section::make(__('Datos del presupuesto'))
                                    ->icon('heroicon-o-calculator')
                                    ->schema([
                                        Forms\Components\TextInput::make('numero_presupuesto')
                                            ->label(__('Número'))
                                            ->disabled()
                                            ->dehydrated()
                                            ->placeholder(__('Auto'))
                                            ->helperText(__('Se genera automáticamente al crear.')),

                                        Forms\Components\DatePicker::make('fecha_presupuesto')
                                            ->label(__('Fecha'))
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->default(now()),

                                        Forms\Components\Select::make('estado')
                                            ->label(__('Estado'))
                                            ->options([
                                                'ofertado'  => __('Ofertado'),
                                                'aceptado'  => __('Aceptado'),
                                                'rechazado' => __('Rechazado'),
                                            ])
                                            ->default('ofertado')
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('enlace')
                                            ->label(__('Enlace'))
                                            ->url()
                                            ->placeholder('https://')
                                            ->maxLength(2048)
                                            ->suffixIcon('heroicon-m-arrow-top-right-on-square')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                            Forms\Components\Section::make(__('Notas'))
                                ->icon('heroicon-o-pencil-square')
                                ->schema([
                                    Forms\Components\Textarea::make('notas')
                                        ->hiddenLabel()
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])
                                ->collapsible()
                                ->collapsed(),
                        ]),

                    Tabs\Tab::make(__('Líneas'))
                        ->icon('heroicon-o-list-bullet')
                        ->badge(fn (?Presupuesto $record) => $record?->lineas()->count() ?: null)
                        ->schema([
                            Forms\Components\Repeater::make('lineas')
                                ->hiddenLabel()
                                ->relationship()
                                ->live(debounce: 500)
                                ->schema([
                                    Forms\Components\TextInput::make('descripcion')
                                        ->label(__('Descripción'))
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(2),

                                    Forms\Components\TextInput::make('cantidad')
                                        ->label(__('Cant.'))
                                        ->numeric()
                                        ->default(1)
                                        ->required(),

                                    Forms\Components\TextInput::make('unidad')
                                        ->label(__('Unidad'))
                                        ->maxLength(30)
                                        ->placeholder('ud, kg, L...'),

                                    Forms\Components\TextInput::make('precio_unitario')
                                        ->label(__('Precio unit.'))
                                        ->numeric()
                                        ->prefix('€')
                                        ->default(0),

                                    Forms\Components\Select::make('estado')
                                        ->label(__('Estado'))
                                        ->options([
                                            'ofertado'  => __('Ofertado'),
                                            'aceptado'  => __('Aceptado'),
                                            'rechazado' => __('Rechazado'),
                                        ])
                                        ->default('ofertado')
                                        ->required(),

                                    Forms\Components\Textarea::make('notas')
                                        ->label(__('Notas'))
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ])
                                ->columns(5)
                                ->defaultItems(1)
                                ->reorderable(false)
                                ->cloneable()
                                ->itemLabel(function (array $state): ?string {
                                    if (empty($state['descripcion'])) return null;
                                    $sub = (float) ($state['cantidad'] ?? 0) * (float) ($state['precio_unitario'] ?? 0);
                                    return trim(($state['cantidad'] ?? '') . ' ' . ($state['unidad'] ?? '') . ' — ' . $state['descripcion'])
                                        . '   (' . number_format($sub, 2, ',', '.') . ' €)';
                                })
                                ->collapsible()
                                ->addActionLabel(__('Añadir línea')),

                            Forms\Components\Placeholder::make('total_calculado')
                                ->label(__('Total estimado'))
                                ->content(function (Get $get) {
                                    $total = collect($get('lineas') ?? [])
                                        ->sum(fn ($l) => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio_unitario'] ?? 0));
                                    return number_format($total, 2, ',', '.') . ' €';
                                })
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_presupuesto')
                    ->label(__('Número'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->url(fn (Presupuesto $record): ?string => $record->enlace ?: null)
                    ->openUrlInNewTab()
                    ->icon(fn (Presupuesto $record): ?string => $record->enlace ? 'heroicon-m-arrow-top-right-on-square' : null)
                    ->color(fn (Presupuesto $record): ?string => $record->enlace ? 'primary' : null),

                Tables\Columns\TextColumn::make('fecha_presupuesto')
                    ->label(__('Fecha'))
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')
                    ->label(__('Cliente'))
                    ->searchable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('escala.barco.nombre')
                    ->label(__('Barco'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('escala.puerto')
                    ->label(__('Escala'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('lineas_count')
                    ->label(__('Líneas'))
                    ->counts('lineas')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('EUR')
                    ->alignEnd()
                    ->weight('semibold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('estado')
                    ->label(__('Estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __(ucfirst($state)))
                    ->color(fn (string $state) => match ($state) {
                        'ofertado'  => 'warning',
                        'aceptado'  => 'success',
                        'rechazado' => 'danger',
                        default     => 'gray',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'ofertado'  => 'heroicon-m-clock',
                        'aceptado'  => 'heroicon-m-check-circle',
                        'rechazado' => 'heroicon-m-x-circle',
                        default     => null,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label(__('Estado'))
                    ->options([
                        'ofertado'  => __('Ofertado'),
                        'aceptado'  => __('Aceptado'),
                        'rechazado' => __('Rechazado'),
                    ]),

                Tables\Filters\SelectFilter::make('escala_id')
                    ->label(__('Escala'))
                    ->options(fn () => Escala::with('barco')
                        ->orderBy('fecha', 'desc')
                        ->get()
                        ->mapWithKeys(fn ($e) => [
                            $e->id => $e->barco?->nombre . ' — ' . $e->puerto . ' (' . ($e->fecha?->format('d/m/Y') ?? '—') . ')',
                        ]))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip(__('Ver')),
                Tables\Actions\EditAction::make()->iconButton()->tooltip(__('Editar')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(__('Eliminar')),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('fecha_presupuesto', 'desc')
            ->emptyStateHeading(__('Sin presupuestos'))
            ->emptyStateDescription(__('Crea el primer presupuesto vinculado a una escala.'))
            ->emptyStateIcon('heroicon-o-calculator');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPresupuestos::route('/'),
            'create' => Pages\CreatePresupuesto::route('/create'),
            'view'   => Pages\ViewPresupuesto::route('/{record}'),
            'edit'   => Pages\EditPresupuesto::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero_presupuesto', 'escala.barco.nombre', 'escala.barco.cliente.nombre'];
    }
}
