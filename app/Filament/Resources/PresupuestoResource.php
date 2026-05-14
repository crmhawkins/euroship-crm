<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresupuestoResource\Pages;
use App\Models\Barco;
use App\Models\Escala;
use App\Models\Presupuesto;
use Filament\Forms;
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
        return $form->schema([
            Forms\Components\Section::make(__('Asignación'))->schema([
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
                                $e->id => $e->puerto . ' (' . ($e->fecha?->format('Y-m-d') ?? '—') . ')',
                            ]);
                    })
                    ->searchable()
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make(__('Datos del presupuesto'))->schema([
                Forms\Components\TextInput::make('numero_presupuesto')
                    ->label(__('Número'))
                    ->disabled()
                    ->dehydrated()
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
                    ->required(),

                Forms\Components\Textarea::make('notas')
                    ->label(__('Notas'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make(__('Líneas'))->schema([
                Forms\Components\Repeater::make('lineas')
                    ->label(__('Líneas del presupuesto'))
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('descripcion')
                            ->label(__('Descripción'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('cantidad')
                            ->label(__('Cantidad'))
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
                    ->itemLabel(fn (array $state): ?string => $state['descripcion'] ?? null)
                    ->collapsible(),
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_presupuesto')
                    ->label(__('Fecha'))
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('escala.barco.cliente.nombre')
                    ->label(__('Cliente'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('escala.barco.nombre')
                    ->label(__('Barco'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('escala.puerto')
                    ->label(__('Escala'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('lineas_count')
                    ->label(__('Líneas'))
                    ->counts('lineas')
                    ->badge(),

                Tables\Columns\TextColumn::make('estado')
                    ->label(__('Estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => __(ucfirst($state)))
                    ->color(fn (string $state) => match ($state) {
                        'ofertado'  => 'warning',
                        'aceptado'  => 'success',
                        'rechazado' => 'danger',
                        default     => 'gray',
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
                        ])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('fecha_presupuesto', 'desc');
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
